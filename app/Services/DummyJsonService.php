<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class DummyJsonService
{
    public function __construct(
        private readonly string $baseUrl = 'https://dummyjson.com'
    ) {}

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function fetchSmartphones(): Collection
    {
        $response = Http::timeout(10)
            ->get("{$this->baseUrl}/products/category/smartphones", [
                'limit' => 0,
            ]);

        if ($response->failed()) {
            throw new RuntimeException('Failed to fetch products from DummyJSON: ' . $response->status());
        }

        return collect($response->json('products', []));
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    public function mapToProductAttributes(array $item): array
    {
        return [
            'external_id' => $item['id'] ?? null,
            'title' => $item['title'] ?? '',
            'description' => $item['description'] ?? null,
            'brand' => $item['brand'] ?? null,
            'category' => $item['category'] ?? null,
            'sku' => $item['sku'] ?? null,
            'price' => $item['price'] ?? 0,
            'discount_percentage' => $item['discountPercentage'] ?? 0,
            'rating' => $item['rating'] ?? 0,
            'stock' => $item['stock'] ?? 0,
            'thumbnail' => $item['thumbnail'] ?? null,
            'images' => $item['images'] ?? [],
            'tags' => $item['tags'] ?? [],
        ];
    }
}
