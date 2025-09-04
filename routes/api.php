<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/tokens/create', function (Request $request) {
    // Forçar debug aqui

    //$user = User::where('email', $request->email);

    $auth = Auth::attempt(['email' => $request->email, 'password' => $request->password]);

    $userToken = $request->user()->createToken('token-api');

    return response()->json([
        'access_token' => $userToken->plainTextToken,
    ]);
});
