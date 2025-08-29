<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Ruta de login para evitar errores de route('login') no definida
Route::get('/login', function () {
    if (request()->expectsJson()) {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => 'UNAUTHENTICATED',
                'message' => 'Esta es una API. Por favor, usa el endpoint de login de la API.',
                'details' => [
                    'api_login_endpoint' => '/api/v1/auth/login',
                    'method' => 'POST',
                    'required_fields' => ['email', 'password']
                ]
            ]
        ], 401);
    }
    
    return response()->json([
        'message' => 'Esta es una API. Por favor, usa el endpoint /api/v1/auth/login para autenticarte.',
        'login_endpoint' => url('/api/v1/auth/login'),
        'method' => 'POST'
    ]);
})->name('login');
