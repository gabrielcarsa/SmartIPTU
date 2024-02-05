<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Autenticação bem-sucedida
            $user = Auth::user();
            return response()->json(['user' => $user]);
        } else {
            // Autenticação falhou
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
    }
}
