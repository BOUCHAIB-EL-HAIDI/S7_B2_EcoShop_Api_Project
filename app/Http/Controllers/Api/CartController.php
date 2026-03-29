<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Product;
class CartController extends Controller
{
    public function index(Request $request){

    $cartItems = $request->user()->cartItems()->with('product')->get();

     return response()->json($cartItems);

    }

     public function store(Request $request)
     {

     $request->validate([
      'product_id' => 'required|exists:products,id',
      'quantity'   => 'required|integer|min:1',


     ]);

     $product = Product::find($request->product_id);

     if($product->stock < $request->quantity)
      {
        return response()->json([

        'message' => 'Not enough stock available'
        ], 400);

      }

      $cartItem = $request->user()->cartItems()->where('product_id', $product->id)->first();

          if ($cartItem) {

             $newQuantity = $cartItem->quantity + $request->quantity;

             if ($newQuantity > $product->stock) {
                 return response()->json([
                     'message' => 'Not enough stock available'
                 ], 400);
             }

             $cartItem->quantity = $newQuantity;
             $cartItem->save();
        } else {

            $cartItem = $request->user()->cartItems()->create([
                'product_id' => $product->id,
                'quantity'   => $request->quantity,
            ]);
        }

        return response()->json([
            'message' => 'Product added to cart',
            'cart_item' => $cartItem
        ], 201);
    }

        public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = $request->user()->cartItems()->find($id);

        if (!$cartItem) {
            return response()->json(['message' => 'Cart item not found'], 404);
        }

        $product = $cartItem->product;

        if ($request->quantity > $product->stock) {
            return response()->json(['message' => 'Not enough stock available'], 400);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json([
            'message' => 'Cart item updated',
            'cart_item' => $cartItem
        ]);
    }


    public function destroy(Request $request, $id)
    {
        $cartItem = $request->user()->cartItems()->find($id);

        if (!$cartItem) {
            return response()->json(['message' => 'Cart item not found'], 404);
        }

        $cartItem->delete();

        return response()->json(['message' => 'Cart item removed']);
    }

}
