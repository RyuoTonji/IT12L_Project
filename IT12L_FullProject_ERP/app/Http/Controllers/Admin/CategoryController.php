<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Active categories list
    public function index()
    {
        $categories = Category::orderBy('id', 'desc')->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    // Show archived categories
    public function archived()
    {
        $categories = Category::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(20);

        return view('admin.categories.archived', compact('categories'));
    }

    // Restore a category
    public function restore($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->restore();

        return redirect()
            ->route('admin.categories.archived')
            ->with('success', 'Category restored successfully!');
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:crm_categories,name',
            'description' => 'nullable|string'
        ]);

        Category::create($request->only(['name', 'description']));

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category created successfully!');
    } // <- This closing brace was missing!

    public function edit($id)
    {
        $category = Category::withTrashed()->findOrFail($id);

        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::withTrashed()->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100|unique:crm_categories,name,' . $id,
            'description' => 'nullable|string'
        ]);

        $category->update($request->only(['name', 'description']));

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category updated successfully!');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        // Prevent archiving if category has products
        if ($category->products()->count() > 0) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', 'Cannot archive category with existing products!');
        }

        $category->delete(); // Soft delete → moves to archive

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category moved to archive!');
    }
}