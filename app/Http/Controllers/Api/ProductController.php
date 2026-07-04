<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\DummyJsonService;

class ProductController extends Controller
{
    public function __construct(
        private readonly DummyJsonService $dummyJson
    ) {}

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

        return ProductResource::collection($products)->response();
    }

    /**
     * GET /api/products/{id}
     */
    public function show(Product $product): JsonResponse
    {
        return (new ProductResource($product))->response();
    }

    /**
     * POST /api/products
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());

        return (new ProductResource($product))->response()->setStatusCode(201);
    }

    /**
     * PATCH /api/products/{id}
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());

        return (new ProductResource($product))->response();
    }

    /**
     * DELETE /api/products/{id}
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json(null, 204);
    }

    /**
     * POST /api/products/seed
     */
    public function seed(): JsonResponse
    {
        $items = $this->dummyJson->fetchSmartphones();

        $imported = 0;
        $updated = 0;

        foreach ($items as $item) {
            $attributes = $this->dummyJson->mapToProductAttributes($item);

            $product = Product::updateOrCreate(
                ['external_id' => $attributes['external_id']],
                $attributes
            );

            $product->wasRecentlyCreated ? $imported++ : $updated++;
        }

        return response()->json([
            'message' => 'Products imported from DummyJSON.',
            'imported' => $imported,
            'updated' => $updated,
            'total' => $items->count(),
        ]);
    }
}
