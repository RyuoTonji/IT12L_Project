<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $inventories = DB::table('inventories')->get();

        foreach ($inventories as $inventory) {
            $name = strtolower($inventory->name);
            $newCategory = 'Ingredients'; // Default

            // Logic for "Prepared Menu" (ready to eat / portioned)
            if (
                str_contains($name, 'bbq') ||
                str_contains($name, 'set') ||
                str_contains($name, 'portion') ||
                str_contains($name, 'liempo') ||
                str_contains($name, 'bangus') ||
                str_contains($name, 'tilapia') ||
                str_contains($name, 'sisig') ||
                str_contains($name, 'grilled')
            ) {
                $newCategory = 'Prepared Menu';
            }

            // Logic for "Others" (drinks, tools, utensils)
            if (
                str_contains($name, 'coke') ||
                str_contains($name, 'sprite') ||
                str_contains($name, 'water') ||
                str_contains($name, 'royal') ||
                str_contains($name, 'juice') ||
                str_contains($name, 'charcoal') ||
                str_contains($name, 'utensil') ||
                str_contains($name, 'tool') ||
                str_contains($name, 'cup') ||
                str_contains($name, 'spoon') ||
                str_contains($name, 'fork')
            ) {
                $newCategory = 'Others';
            }

            DB::table('inventories')->where('id', $inventory->id)->update([
                'category' => $newCategory
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No easy way to reverse this specifically, leaving empty
    }
};
