<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VoidRequest;

class FixReasonTagsEncoding extends Command
{
    protected $signature = 'fix:reason-tags {--debug : Show debug information}';
    protected $description = 'Fix double-encoded reason_tags in void_requests table';

    public function handle()
    {
        $debug = $this->option('debug');

        if ($debug) {
            $this->info('Analyzing reason_tags encoding...');

            $requests = VoidRequest::whereNotNull('reason_tags')->take(5)->get();

            foreach ($requests as $request) {
                $raw = $request->getRawOriginal('reason_tags');
                $this->line("ID: {$request->id}");
                $this->line("  Raw value: " . substr($raw ?? 'NULL', 0, 100));
                $this->line("  Raw type: " . gettype($raw));

                // First JSON decode
                $decoded1 = json_decode($raw, true);
                $this->line("  After 1st decode type: " . gettype($decoded1));
                $this->line("  After 1st decode value: " . (is_array($decoded1) ? json_encode($decoded1) : ($decoded1 ?? 'null')));

                // If string, try second decode
                if (is_string($decoded1)) {
                    $decoded2 = json_decode($decoded1, true);
                    $this->line("  After 2nd decode type: " . gettype($decoded2));
                    $this->line("  After 2nd decode value: " . (is_array($decoded2) ? json_encode($decoded2) : ($decoded2 ?? 'null')));
                }

                $this->line('');
            }
            return Command::SUCCESS;
        }

        $this->info('Fixing double-encoded reason_tags...');

        $fixed = 0;
        $skipped = 0;

        VoidRequest::whereNotNull('reason_tags')->chunk(100, function ($requests) use (&$fixed, &$skipped) {
            foreach ($requests as $request) {
                $raw = $request->getRawOriginal('reason_tags');

                // Skip if null or empty
                if (empty($raw)) {
                    $skipped++;
                    continue;
                }

                // Check if it's a double-encoded string (starts with quote after JSON decode)
                $decoded = json_decode($raw, true);

                // If decoded result is a string (not array), it's double-encoded
                if (is_string($decoded)) {
                    // Decode again to get the actual array
                    $actualArray = json_decode($decoded, true);

                    if (is_array($actualArray)) {
                        // Update with proper array value (model cast will handle encoding)
                        $request->reason_tags = $actualArray;
                        $request->save();
                        $fixed++;
                        $this->line("Fixed record ID: {$request->id}");
                    } else {
                        $skipped++;
                        $this->warn("Could not decode ID: {$request->id}");
                    }
                } elseif (is_array($decoded)) {
                    // Already correct
                    $skipped++;
                }
            }
        });

        $this->info("Done! Fixed: {$fixed}, Skipped: {$skipped}");

        return Command::SUCCESS;
    }
}
