<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class MenuController extends Controller
{
    public function index()
    {
        $menuItems = MenuItem::select('menu_items.*')
            ->join('categories', 'menu_items.category_id', '=', 'categories.id')
            ->orderBy('categories.sort_order')
            ->orderBy('menu_items.name')
            ->with(['category', 'branches'])
            ->get();

        // Group menu items by category for the view
        $menuItemsByCategory = $menuItems->groupBy(function ($item) {
            return $item->category->name;
        });

        return view('admin.menu.index', compact('menuItems', 'menuItemsByCategory'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.menu.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'boolean',
        ]);

        $data = $request->except('image');
        $data['is_available'] = $request->boolean('is_available');

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            // Store the original image
            $path = $image->storeAs('menu-items', $filename, 'public');

            // Create a thumbnail
            $thumbnail = Image::make($image)->resize(300, 200, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $thumbnailPath = 'menu-items/thumbnails/' . $filename;
            Storage::disk('public')->put($thumbnailPath, (string) $thumbnail->encode());

            $data['image'] = $path;
        }

        $menuItem = MenuItem::create($data);

        // Attach to both branches by default with availability matching the menu item's is_available status
        $menuItem->branches()->attach([
            1 => ['is_available' => $data['is_available']],
            2 => ['is_available' => $data['is_available']],
        ]);

        return redirect()->route('menu.index')->with('success', 'Menu item created successfully');
    }

    public function show(MenuItem $menu)
    {
        // Get sales data for the last 30 days
        $endDate = \Carbon\Carbon::now();
        $startDate = \Carbon\Carbon::now()->subDays(29);

        $salesData = \Illuminate\Support\Facades\DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select(
                \Illuminate\Support\Facades\DB::raw('DATE(orders.created_at) as date'),
                \Illuminate\Support\Facades\DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_sales'),
                \Illuminate\Support\Facades\DB::raw('SUM(order_items.quantity) as total_quantity')
            )
            ->where('order_items.menu_item_id', $menu->id)
            ->where('orders.payment_status', 'paid')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill in missing dates with zero values
        $formattedSales = [];
        $currentDate = clone $startDate;
        $salesKeyed = $salesData->keyBy('date');

        while ($currentDate <= $endDate) {
            $dateString = $currentDate->format('Y-m-d');
            $record = $salesKeyed->get($dateString);

            $formattedSales[] = [
                'date' => $currentDate->format('M d'),
                'total_sales' => $record ? $record->total_sales : 0,
                'total_quantity' => $record ? $record->total_quantity : 0,
            ];

            $currentDate->addDay();
        }

        return view('admin.menu.show', compact('menu', 'formattedSales'));
    }

    public function edit(MenuItem $menu)
    {
        $categories = Category::all();
        return view('admin.menu.edit', compact('menu', 'categories'));
    }

    public function update(Request $request, MenuItem $menu)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'boolean',
        ]);

        $data = $request->except('image');
        $data['is_available'] = $request->has('is_available');

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($menu->image && Storage::disk('public')->exists($menu->image)) {
                Storage::disk('public')->delete($menu->image);

                // Also delete thumbnail if it exists
                $thumbnailPath = str_replace('menu-items/', 'menu-items/thumbnails/', $menu->image);
                if (Storage::disk('public')->exists($thumbnailPath)) {
                    Storage::disk('public')->delete($thumbnailPath);
                }
            }

            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            // Store the original image
            $path = $image->storeAs('menu-items', $filename, 'public');

            // Create a thumbnail
            $thumbnail = Image::make($image)->resize(300, 200, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $thumbnailPath = 'menu-items/thumbnails/' . $filename;
            Storage::disk('public')->put($thumbnailPath, (string) $thumbnail->encode());

            $data['image'] = $path;
        }

        $menu->update($data);

        // Sync branch availability if not explicitly set via the branch availability endpoint
        // Only update if menu item doesn't have branch associations yet
        if ($menu->branches()->count() == 0) {
            $menu->branches()->attach([
                1 => ['is_available' => $data['is_available']],
                2 => ['is_available' => $data['is_available']],
            ]);
        }

        return redirect()->route('menu.index')->with('success', 'Menu item updated successfully');
    }

    public function destroy(MenuItem $menu)
    {
        $menu->delete();

        return redirect()->route('menu.index')->with('success', 'Menu item archived successfully');
    }

    /**
     * Update menu item availability status
     */
    public function updateAvailability(Request $request, MenuItem $menu)
    {
        $request->validate([
            'is_available' => 'required|boolean',
        ]);

        $isAvailable = $request->is_available;

        // Update the menu item's global availability
        $menu->update([
            'is_available' => $isAvailable,
        ]);

        // Sync branch availability - update all branches to match global availability
        // Get existing branch associations
        $branches = $menu->branches()->pluck('branches.id')->toArray();

        if (!empty($branches)) {
            $syncData = [];
            foreach ($branches as $branchId) {
                $syncData[$branchId] = ['is_available' => $isAvailable];
            }
            $menu->branches()->sync($syncData);
        }

        return redirect()->route('menu.index')->with('success', 'Availability updated successfully');
    }

    /**
     * Update menu item branch availability
     */
    public function updateBranchAvailability(Request $request, MenuItem $menu)
    {
        $request->validate([
            'branch' => 'required|in:branch1,branch2,both',
        ]);

        $branch = $request->branch;

        // Sync branches based on selection
        if ($branch === 'both') {
            // Attach to both branches, mark as available
            $menu->branches()->sync([
                1 => ['is_available' => true],
                2 => ['is_available' => true],
            ]);
        } elseif ($branch === 'branch1') {
            // Only Branch 1, mark Branch 2 as unavailable
            $menu->branches()->sync([
                1 => ['is_available' => true],
                2 => ['is_available' => false],
            ]);
        } elseif ($branch === 'branch2') {
            // Only Branch 2, mark Branch 1 as unavailable
            $menu->branches()->sync([
                1 => ['is_available' => false],
                2 => ['is_available' => true],
            ]);
        }

        return redirect()->route('menu.index')->with('success', 'Branch availability updated successfully');
    }
}
