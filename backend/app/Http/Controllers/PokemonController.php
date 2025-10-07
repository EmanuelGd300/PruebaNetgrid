<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PokemonController extends Controller
{
    private $baseUrl = 'https://pokeapi.co/api/v2';

    public function index(Request $request)
    {
        $limit = min($request->get('limit', 20), 50); // Máximo 50
        $page = $request->get('page', 1);
        $type = $request->get('type');
        $offset = ($page - 1) * $limit;

        // Si hay filtro por tipo
        if ($type) {
            $typeResponse = Http::get("{$this->baseUrl}/type/{$type}");
            if ($typeResponse->failed()) {
                return response()->json(['error' => 'Tipo no encontrado'], 404);
            }
            
            $typeData = $typeResponse->json();
            $pokemonUrls = collect($typeData['pokemon'])
                ->pluck('pokemon.url')
                ->slice($offset, $limit);
                
            $pokemonList = [];
            foreach ($pokemonUrls as $url) {
                $pokemonResponse = Http::get($url);
                if ($pokemonResponse->successful()) {
                    $pokemonData = $pokemonResponse->json();
                    $pokemonList[] = [
                        'id' => $pokemonData['id'],
                        'name' => $pokemonData['name'],
                        'image' => $pokemonData['sprites']['front_default'],
                        'types' => array_map(fn($type) => $type['type']['name'], $pokemonData['types']),
                    ];
                }
            }
            
            return response()->json([
                'results' => $pokemonList,
                'count' => count($typeData['pokemon']),
                'current_page' => $page,
                'per_page' => $limit,
                'total_pages' => ceil(count($typeData['pokemon']) / $limit),
            ]);
        }

        $response = Http::get("{$this->baseUrl}/pokemon", [
            'limit' => $limit,
            'offset' => $offset,
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Error al obtener pokémon'], 500);
        }

        $data = $response->json();
        
        // Obtener detalles de cada pokémon
        $pokemonList = [];
        foreach ($data['results'] as $pokemon) {
            $pokemonResponse = Http::get($pokemon['url']);
            if ($pokemonResponse->successful()) {
                $pokemonData = $pokemonResponse->json();
                $pokemonList[] = [
                    'id' => $pokemonData['id'],
                    'name' => $pokemonData['name'],
                    'image' => $pokemonData['sprites']['front_default'],
                    'types' => array_map(fn($type) => $type['type']['name'], $pokemonData['types']),
                ];
            }
        }

        return response()->json([
            'results' => $pokemonList,
            'count' => $data['count'],
            'current_page' => $page,
            'per_page' => $limit,
            'total_pages' => ceil($data['count'] / $limit),
            'next' => $data['next'],
            'previous' => $data['previous'],
        ]);
    }

    public function show($id)
    {
        $response = Http::get("{$this->baseUrl}/pokemon/{$id}");

        if ($response->failed()) {
            return response()->json(['error' => 'Pokémon no encontrado'], 404);
        }

        $pokemon = $response->json();

        // Obtener información de la especie para la descripción
        $speciesResponse = Http::get($pokemon['species']['url']);
        $description = '';
        
        if ($speciesResponse->successful()) {
            $species = $speciesResponse->json();
            $flavorTexts = collect($species['flavor_text_entries'])
                ->where('language.name', 'es')
                ->first();
            
            if (!$flavorTexts) {
                $flavorTexts = collect($species['flavor_text_entries'])
                    ->where('language.name', 'en')
                    ->first();
            }
            
            $description = $flavorTexts ? $flavorTexts['flavor_text'] : '';
        }

        return response()->json([
            'id' => $pokemon['id'],
            'name' => $pokemon['name'],
            'image' => $pokemon['sprites']['front_default'],
            'types' => array_map(fn($type) => $type['type']['name'], $pokemon['types']),
            'height' => $pokemon['height'],
            'weight' => $pokemon['weight'],
            'abilities' => array_map(fn($ability) => $ability['ability']['name'], $pokemon['abilities']),
            'stats' => array_map(fn($stat) => [
                'name' => $stat['stat']['name'],
                'value' => $stat['base_stat']
            ], $pokemon['stats']),
            'description' => $description,
        ]);
    }

    public function search(Request $request)
    {
        $query = strtolower($request->get('q'));
        
        if (!$query) {
            return response()->json(['error' => 'Parámetro de búsqueda requerido'], 400);
        }

        // Primero intentar búsqueda exacta
        $response = Http::get("{$this->baseUrl}/pokemon/{$query}");

        if ($response->successful()) {
            $pokemon = $response->json();
            return response()->json([
                'results' => [[
                    'id' => $pokemon['id'],
                    'name' => $pokemon['name'],
                    'image' => $pokemon['sprites']['front_default'],
                    'types' => array_map(fn($type) => $type['type']['name'], $pokemon['types']),
                ]]
            ]);
        }

        // Si no encuentra por nombre exacto, buscar en lista general
        $allPokemonResponse = Http::get("{$this->baseUrl}/pokemon?limit=1000");
        
        if ($allPokemonResponse->failed()) {
            return response()->json(['results' => []]);
        }

        $allPokemon = $allPokemonResponse->json()['results'];
        $matches = [];

        foreach ($allPokemon as $pokemon) {
            if (str_contains(strtolower($pokemon['name']), $query)) {
                $pokemonResponse = Http::get($pokemon['url']);
                if ($pokemonResponse->successful()) {
                    $pokemonData = $pokemonResponse->json();
                    $matches[] = [
                        'id' => $pokemonData['id'],
                        'name' => $pokemonData['name'],
                        'image' => $pokemonData['sprites']['front_default'],
                        'types' => array_map(fn($type) => $type['type']['name'], $pokemonData['types']),
                    ];
                    
                    // Limitar resultados para evitar sobrecarga
                    if (count($matches) >= 20) break;
                }
            }
        }

        return response()->json(['results' => $matches]);
    }
}