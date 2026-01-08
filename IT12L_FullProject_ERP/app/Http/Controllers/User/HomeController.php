<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $branches = Branch::all();

        return view('user.home.index', compact('branches'));
    }

    public function browse($id)
    {
        $branch = Branch::find($id);

        if (!$branch) {
            return redirect()->route('home')->with('error', 'Branch not found!');
        }

        $categories = Category::all();

        $products = Product::byBranch($id)
            ->with('category')
            ->paginate(12);

        return view('user.home.browse', compact('branch', 'categories', 'products'));
    }

    public function filterByCategory($branchId, $categoryId)
    {
        $branch = Branch::find($branchId);

        if (!$branch) {
            return redirect()->route('home')->with('error', 'Branch not found!');
        }

        $categories = Category::all();

        $products = Product::byBranch($branchId)
            ->where('category_id', $categoryId)
            ->with('category')
            ->paginate(12);

        return view('user.home.browse', compact('branch', 'categories', 'products'));
    }
}