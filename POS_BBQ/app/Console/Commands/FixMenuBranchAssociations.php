<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MenuItem;

class FixMenuBranchAssociations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'menu:fix-branch-associations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix existing menu items that are not associated with any branches';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking menu items without branch associations...');

        $menuItemsWithoutBranches = MenuItem::doesntHave('branches')->get();

        if ($menuItemsWithoutBranches->isEmpty()) {
            $this->info('✅ All menu items already have branch associations!');
            return 0;
        }

        $this->info("Found {$menuItemsWithoutBranches->count()} menu items without branch associations.");

        $bar = $this->output->createProgressBar($menuItemsWithoutBranches->count());
        $bar->start();

        foreach ($menuItemsWithoutBranches as $menuItem) {
            // Attach to both branches with the menu item's current is_available status
            $menuItem->branches()->attach([
                1 => ['is_available' => $menuItem->is_available],
                2 => ['is_available' => $menuItem->is_available],
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Successfully attached {$menuItemsWithoutBranches->count()} menu items to both branches!");
        $this->info('📊 Branch availability set to match each menu item\'s is_available status.');

        return 0;
    }
}
