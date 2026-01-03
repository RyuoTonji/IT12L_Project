<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

class SyncModelToSupabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The model instance/details to sync.
     */
    protected $modelClass;
    protected $modelAttributes;
    protected $modelId;
    protected $action;

    /**
     * Create a new job instance.
     *
     * @param string $modelClass The class name of the model
     * @param array $modelAttributes The attributes to sync (for create/update)
     * @param mixed $modelId The primary key of the model
     * @param string $action 'save' or 'delete'
     */
    public function __construct($modelClass, $modelAttributes, $modelId, $action = 'save')
    {
        $this->modelClass = $modelClass;
        $this->modelAttributes = $modelAttributes;
        $this->modelId = $modelId;
        $this->action = $action;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Removed explicit functionality check to rely on try-catch block for connection errors
        // This avoids false negatives where fsockopen might timeout but the connection is actually valid

        try {
            // Replicate the action on the 'supabase' connection
            $table = (new $this->modelClass)->getTable();

            if ($this->action === 'delete') {
                DB::connection('supabase')->table($table)
                    ->where('id', $this->modelId)
                    ->delete();

                Log::info("Synced delete to Supabase for {$this->modelClass} ID: {$this->modelId}");
            } else {
                // Update or Insert
                // We use updateOrInsert to handle both creation and updates idempotently
                DB::connection('supabase')->table($table)->updateOrInsert(
                    ['id' => $this->modelId],
                    $this->modelAttributes
                );

                Log::info("Synced save to Supabase for {$this->modelClass} ID: {$this->modelId}");
            }

        } catch (\Exception $e) {
            Log::error("Failed to sync to Supabase: " . $e->getMessage());
            // Rethrow to trigger Laravel's retry mechanism (exponential backoff configured in queue worker or job)
            throw $e;
        }
    }

    public function backoff(): array
    {
        // Exponential backoff up to 1 hour (3600s)
        return [1, 5, 10, 30, 60, 120, 300, 600, 1800, 3600];
    }

    /**
     * Check if there is an active connection to the Supabase host.
     */
    protected function isOnline(): bool
    {
        $host = config('database.connections.supabase.host');
        $port = config('database.connections.supabase.port');

        // Simple socket connection check
        $connection = @fsockopen($host, $port, $errno, $errstr, 2); // 2 second timeout

        if (is_resource($connection)) {
            fclose($connection);
            return true;
        }

        return false;
    }
}
