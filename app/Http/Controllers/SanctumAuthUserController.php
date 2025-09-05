<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SanctumAuthUserController extends Controller
{
    public function __construct(User $user) {}

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|max:255'
        ]);

        // Sanitização específica para email
        $email = filter_var($request->email, FILTER_SANITIZE_EMAIL);

        $login = Auth::attempt([
            'email' => $email,
            'password' => $request->password, // Senha NÃO deve ser sanitizada
        ]);

        if (!$login) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Auth::user();
        /** @var User $user */
        $token = $user->createToken('api-token');

        return response()->json([
            'user' => $user,
            'access_token' => $token->plainTextToken,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
