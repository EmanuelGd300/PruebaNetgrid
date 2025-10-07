<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $favorites = $request->user()->favorites()->orderBy('created_at', 'desc')->get();
        
        return response()->json($favorites);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pokemon_id' => 'required|string',
            'name' => 'required|string',
            'image' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $favorite = $request->user()->favorites()->updateOrCreate(
            ['pokemon_id' => $request->pokemon_id],
            [
                'name' => $request->name,
                'image' => $request->image,
                'description' => $request->description,
            ]
        );

        return response()->json($favorite, 201);
    }

    public function destroy(Request $request, $pokemonId)
    {
        $favorite = $request->user()->favorites()->where('pokemon_id', $pokemonId)->first();

        if (!$favorite) {
            return response()->json(['message' => 'Favorito no encontrado'], 404);
        }

        $favorite->delete();

        return response()->json(['message' => 'Favorito eliminado exitosamente']);
    }

    public function check(Request $request, $pokemonId)
    {
        $isFavorite = $request->user()->favorites()->where('pokemon_id', $pokemonId)->exists();
        
        return response()->json(['is_favorite' => $isFavorite]);
    }
}