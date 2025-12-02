<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\User;
use App\Models\Table;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SoftDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_can_be_soft_deleted()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::create([
            'name' => 'Test Category',
            'description' => 'Test Description'
        ]);

        $response = $this->actingAs($user)->delete(route('categories.destroy', $category));

        $response->assertRedirect(route('categories.index'));
        $this->assertSoftDeleted($category);
    }

    public function test_menu_item_can_be_soft_deleted_and_image_preserved()
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::create([
            'name' => 'Test Category',
            'description' => 'Test Description'
        ]);

        $image = UploadedFile::fake()->create('pizza.jpg');
        $menuItem = MenuItem::create([
            'category_id' => $category->id,
            'name' => 'Pizza',
            'price' => 10,
            'image' => 'menu-items/pizza.jpg',
            'is_available' => true
        ]);

        // Manually put file to simulate existing image
        Storage::disk('public')->put('menu-items/pizza.jpg', 'content');

        $response = $this->actingAs($user)->delete(route('menu.destroy', $menuItem));

        $response->assertRedirect(route('menu.index'));
        $this->assertSoftDeleted($menuItem);

        // Assert image still exists
        Storage::disk('public')->assertExists('menu-items/pizza.jpg');
    }

    public function test_staff_can_be_soft_deleted()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'cashier']);

        $response = $this->actingAs($admin)->delete(route('staff.destroy', $staff));

        $response->assertRedirect(route('staff.index'));
        $this->assertSoftDeleted($staff);
    }

    public function test_table_can_be_soft_deleted()
    {
        $user = User::factory()->create(['role' => 'cashier']);
        $table = Table::create(['name' => 'T1', 'capacity' => 4, 'status' => 'available']);

        $response = $this->actingAs($user)->delete(route('tables.destroy', $table));

        $response->assertRedirect(route('tables.index'));
        $this->assertSoftDeleted($table);
    }
}
