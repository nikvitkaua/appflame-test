<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * GET /api/products
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::query();

        if ($request->filled('brand')) {
            $query->whereRaw('LOWER(brand) = ?', [mb_strtolower($request->string('brand'))]);
        }

        $perPage = $request->integer('limit', 15);
        $perPage = min(max($perPage, 1), 100);

        $products = $query->orderBy('id')->paginate($perPage)->withQueryString();

        return response()->json($products);
    }

    /**
     * GET /api/products/{id}
     */
    public function show(Product $product): JsonResponse
    {
        return response()->json($product);
    }
}
