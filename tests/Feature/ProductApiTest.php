<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_products_with_pagination(): void
    {
        Product::factory()->count(20)->create();

        $response = $this->getJson('/api/products?limit=5');

        $response->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('meta.per_page', 5)
            ->assertJsonPath('meta.total', 20);
    }

    public function test_brand_products_filter(): void
    {
        Product::factory()->create(['brand' => 'apple']);
        Product::factory()->create(['brand' => 'samsung']);

        $response = $this->getJson('/api/products?brand=apple');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.brand', 'apple');
    }

    public function test_brand_products_filter_with_uppercase(): void
    {
        Product::factory()->create(['brand' => 'apple']);
        Product::factory()->create(['brand' => 'samsung']);

        $response = $this->getJson('/api/products?brand=Apple');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.brand', 'apple');
    }

    public function test_one_product_info(): void
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $product->id)
            ->assertJsonPath('data.title', $product->title);
    }

    public function test_404_response_if_product_does_not_exist(): void
    {
        $this->getJson('/api/products/999')->assertNotFound();
    }

    public function test_create_product(): void
    {
        $payload = [
            'title' => 'Test Phone X',
            'brand' => 'TestBrand',
            'price' => 499.99,
            'stock' => 10,
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.title', 'Test Phone X')
            ->assertJsonPath('data.brand', 'TestBrand');

        $this->assertDatabaseHas('products', ['title' => 'Test Phone X']);
    }

    public function test_rejects_invalid_product_creation(): void
    {
        $response = $this->postJson('/api/products', [
            'title' => '',
            'price' => -10,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'price']);
    }

    public function test_PATCH_method_in_existing_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->patchJson("/api/products/{$product->id}", ['stock' => 42]);

        $response->assertOk()
            ->assertJsonPath('data.stock', 42)
            ->assertJsonPath('data.title', $product->title);
    }

    public function test_deletes_a_product(): void
    {
        $product = Product::factory()->create();

        $this->deleteJson("/api/products/{$product->id}")->assertNoContent();

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_seeds_products_from_dummyjson(): void
    {
        Http::fake([
            'dummyjson.com/*' => Http::response([
                'products' => [
                    [
                        'id' => 1,
                        'title' => 'iPhone 15',
                        'brand' => 'Apple',
                        'price' => 999,
                    ],
                ],
            ], 200),
        ]);

        $response = $this->postJson('/api/products/seed');

        $response->assertOk()
            ->assertJsonPath('imported', 1)
            ->assertJsonPath('updated', 0);

        $this->assertDatabaseHas('products', [
            'external_id' => 1,
            'title' => 'iPhone 15',
        ]);
    }
}
