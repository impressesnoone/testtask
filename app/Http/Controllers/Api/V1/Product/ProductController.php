<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function getAllProducts(Request $request)
    {
        $products = Product::all();
        $categories = Product::getCategories();
        $products_c1 = [];
        $products_c2 = [];
        $flag_cost = null;
        if ($request->filled('category1')) {
            $products_c1 = $products->where('p_id', $request->get('category1'));
        }
        if ($request->filled('category2')) {
            $products_c2 = $products->where('p_id', $request->get('category2'));
        }
        if ($request->filled('cost')) {
            switch ($request->get('cost')) {
                case 'max':
                    $flag_cost = 'max';
                    break;
                case 'min':
                    $flag_cost = 'min';
                    break;
                default:
                    $flag_cost = null;
            }
        }
        if ($products_c1 || $products_c2 || $flag_cost) {
            if ($products_c1) {
                $categories = $products_c1->merge($products_c2)->sortByDesc('cost');
            }
            if ($flag_cost == 'max') {
                $categories = $categories->sortByDesc('cost');
            }
            if ($flag_cost == 'min') {
                $categories = $categories->sortBy('cost');
            }
            return response()->json(['products' => $categories], 200);
        }
        return response()->json(['products' => $categories], 200);
    }

    public function getSlugProduct($slug)
    {
        return response()->json(['product' => Product::where('slug', $slug)->get()], 200);
    }
}
