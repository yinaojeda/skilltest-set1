<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Authentication controller for user registration, login, logout, and profile retrieval.
 */

class AuthController extends Controller
{
    /**
     * User registration
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone'    => 'nullable|string|max:30',
            'role'     => ['required', Rule::in(['admin', 'manager', 'user'])],
        ]);

        $data['password'] = Hash::make('password');

        $user = User::create($data);

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'data'  => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * User login
     */

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'data'  => $user,
            'token' => $token,
        ]);
    }

    /**
     * User logout (revoke current token)
     */

    public function logout(Request $request)
    {
        // revoke ONLY current token
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }

    /**
     * Get the authenticated user's profile
     */
    public function me(Request $request)
    {
        return response()->json(['data' => $request->user()->load(['projects', 'tasks', 'comments'])]);
    }
}
