<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{


   public function register(Request $request)
   {

    $request->validate([
     'name'  => 'required|string|max:255',
     'email'    => 'required|email|unique:users,email',
      'password' => 'required|string|min:6|confirmed',

    ]);


    $user = User::create([

     'name'   =>$request->name,
      'email'  => $request->email,
      'password' =>Hash::make($request->password),
    ]);

    $token = $user->createToken('api_token')->plainTextToken;

    return response()->json([

      'message' => 'User registered successfully',
      'user'  => $user,
      'token'   => $token,
    ], 201);


   }

   public function login(Request $request)
   {
       $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

       $user = User::where('email', $request->email)->first();

       if(!$user || !Hash::check($request->password , $user->password)){

        throw ValidationException::withMessages([

         'email' => ['login failed'],

        ]);

       }

       $token = $user->createToken('api_token')->plainTextToken;

       return response()->json([
        'message' => 'login successful',
        'user'   =>$user ,
        'token'  =>$token,
       ]);

   }


    public function logout(Request $request)
    {
        $user = $request->user();
        
        if ($user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }


    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

}
