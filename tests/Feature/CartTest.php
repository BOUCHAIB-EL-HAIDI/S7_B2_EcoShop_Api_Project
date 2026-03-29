<?php

use App\Models\User;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can add product to cart', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 10]);

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/cart', [
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('cart_items', [
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
});

test('user cannot add more than stock to cart', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 5]);

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/cart', [
        'product_id' => $product->id,
        'quantity' => 10,
    ]);

    $response->assertStatus(400)
        ->assertJson(['message' => 'Not enough stock available']);
});

test('user can update cart item quantity', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 10]);
    $cartItem = CartItem::create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $response = $this->actingAs($user, 'sanctum')->putJson("/api/cart/{$cartItem->id}", [
        'quantity' => 5,
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('cart_items', [
        'id' => $cartItem->id,
        'quantity' => 5,
    ]);
});

test('user can remove item from cart', function () {
    $user = User::factory()->create();
    $cartItem = CartItem::create([
        'user_id' => $user->id,
        'product_id' => Product::factory()->create()->id,
        'quantity' => 1,
    ]);

    $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/cart/{$cartItem->id}");

    $response->assertStatus(200);
    $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
});
