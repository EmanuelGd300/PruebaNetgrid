<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\PokemonController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\TypeController;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/password/email', [PasswordResetController::class, 'sendResetLink']);
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword']);
Route::get('/recaptcha-site-key', function () {
    return response()->json(['site_key' => env('RECAPTCHA_SITE_KEY')]);
});

// Rutas de Pokémon (públicas)
Route::get('/pokemon', [PokemonController::class, 'index']);
Route::get('/pokemon/search', [PokemonController::class, 'search']);
Route::get('/pokemon/{id}', [PokemonController::class, 'show']);
Route::get('/types', [TypeController::class, 'index']);

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'Pokemon API is running',
        'timestamp' => now()->toISOString(),
        'database' => 'sqlite',
        'version' => '1.0.0'
    ]);
});

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    // Autenticación
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Favoritos
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{pokemonId}', [FavoriteController::class, 'destroy']);
    Route::get('/favorites/check/{pokemonId}', [FavoriteController::class, 'check']);
});