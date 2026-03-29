<?php

use App\Models\User;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Order;
use App\Events\OrderPlaced;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can place order from cart', function () {
    Event::fake();

    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 10, 'price' => 100]);

    // Add to cart
    CartItem::create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/checkout');

    $response->assertStatus(201);

    $this->assertDatabaseHas('orders', [
        'user_id' => $user->id,
        'total_price' => 200,
    ]);

    $this->assertDatabaseHas('order_items', [
        'product_id' => $product->id,
        'quantity' => 2,
        'price' => 100,
    ]);

    $this->assertDatabaseMissing('cart_items', [
        'user_id' => $user->id,
    ]);

    Event::assertDispatched(OrderPlaced::class);
});

test('cannot place order with empty cart', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/checkout');

    $response->assertStatus(400)
        ->assertJson(['message' => 'Cart is empty']);
});
