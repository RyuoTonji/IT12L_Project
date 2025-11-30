<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $branches = DB::table('branches')->get();
        
        return view('user.home.index', compact('branches'));
    }

    public function browse($id)
    {
        $branch = DB::table('branches')->where('id', $id)->first();
        
        if (!$branch) {
            return redirect()->route('home')->with('error', 'Branch not found!');
        }

        $categories = DB::table('categories')->get();
        
        $products = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('products.branch_id', $id)
            ->select('products.*', 'categories.name as category_name')
            ->paginate(12);

        return view('user.home.browse', compact('branch', 'categories', 'products'));
    }

    public function filterByCategory($branchId, $categoryId)
    {
        $branch = DB::table('branches')->where('id', $branchId)->first();
        
        if (!$branch) {
            return redirect()->route('home')->with('error', 'Branch not found!');
        }

        $categories = DB::table('categories')->get();
        
        $products = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('products.branch_id', $branchId)
            ->where('products.category_id', $categoryId)
            ->select('products.*', 'categories.name as category_name')
            ->paginate(12);

        return view('user.home.browse', compact('branch', 'categories', 'products'));
    }
}