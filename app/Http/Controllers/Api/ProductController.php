<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{


     public function index(Request $request){
       $query = Product::with('category');

       if ($request->filled('category_id')) {
           $query->where('category_id', $request->category_id);
       }

       $products = $query->paginate(10);

       return response()->json($products);
     }

     public function show($id)
     {
     $product = Product::with('category')->find($id);

     if(!$product){

     return response()->json([

       'message'=>'Product not found'

     ], 404);

     }
     return response()->json($product);

     }

     public function store(Request $request)
     {



      if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Only admins can create products'
            ], 403);
        }


      $request->validate([
        'name'   =>'required|string|max:255|unique:products,name',
        'description' =>'nullable|string',
        'price'  =>'required|numeric',
        'stock'     =>'required|integer',
        'category_id'  =>'required|exists:categories,id',
      ]);

     $product = Product::create($request->all());

     return response()->json([

        'message' => 'Product created successfully',
         'product' => $product

     ], 201 );

     }


     public function update(Request $request , $id)
     {


     if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Only admins can update products'
            ], 403);
        }

      $product = Product::find($id);

       if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }

    $request->validate([
        'name'   =>'required|string|max:255|unique:products,name,' . $id,
        'description' =>'nullable|string',
        'price'  =>'required|numeric',
        'stock'     =>'required|integer',
        'category_id'  =>'required|exists:categories,id',
      ]);

      $product->update($request->all());

      return response()->json([

        'message' => 'Product updated successfully',
        'product' => $product

      ]);

     }


     public function destroy(Request $request, $id)
    {

      if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Only admins can delete products'
            ], 403);
        }



        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }

}
