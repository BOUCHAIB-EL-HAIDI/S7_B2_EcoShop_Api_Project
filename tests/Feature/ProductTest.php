<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest can see products', function () {
    Product::factory(3)->create();

    $response = $this->getJson('/api/products');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

test('guest can see single product', function () {
    $product = Product::factory()->create();

    $response = $this->getJson("/api/products/{$product->id}");

    $response->assertStatus(200)
        ->assertJson([
            'id' => $product->id,
            'name' => $product->name,
        ]);
});

test('admin can create product', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create();

    $response = $this->actingAs($admin, 'sanctum')->postJson('/api/products', [
        'name' => 'Eco Bottle',
        'description' => 'A very eco bottle',
        'price' => 12.99,
        'stock' => 50,
        'category_id' => $category->id,
    ]);

    $response->assertStatus(201)
        ->assertJsonFragment(['name' => 'Eco Bottle']);

    $this->assertDatabaseHas('products', ['name' => 'Eco Bottle']);
});

test('client cannot create product', function () {
    $client = User::factory()->create();
    $category = Category::factory()->create();

    $response = $this->actingAs($client, 'sanctum')->postJson('/api/products', [
        'name' => 'Eco Bottle',
        'description' => 'A very eco bottle',
        'price' => 12.99,
        'stock' => 50,
        'category_id' => $category->id,
    ]);

    $response->assertStatus(403);
});
