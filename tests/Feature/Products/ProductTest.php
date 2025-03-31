<?php

namespace Tests\Feature\Products;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_can_be_indexed()
    {
        Product::factory(5)->create();

        $response = $this->getJson('/api/products');

        $response->assertOk()
            ->assertJsonCount(5); // Или ->assertJsonCount(5, 'data') если изменили контроллер
    }

    public function test_product_can_be_shown()
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertOk()
            ->assertJson([
                'id' => $product->id,
                'sku' => $product->sku,
                'name' => $product->name,
                'price' => $product->price,
            ]);
    }

    public function test_product_can_be_stored()
    {
        $productData = [
            'sku' => 'TEST-123',
            'name' => 'Test Product',
            'price' => 19.99,
        ];

        $response = $this->postJson('/api/products', $productData);

        $response->assertCreated()
            ->assertJson($productData);

        $this->assertDatabaseHas('products', $productData);
    }

    public function test_product_can_be_updated()
    {
        $product = Product::factory()->create();

        $updateData = [
            'name' => 'Updated Name',
            'price' => 29.99,
        ];

        $response = $this->putJson("/api/products/{$product->id}", $updateData);

        $response->assertOk()
            ->assertJson($updateData);

        $this->assertDatabaseHas('products', $updateData);
    }

    public function test_product_can_be_destroyed()
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}