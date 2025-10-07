<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PokemonService
{
    private string $baseUrl = 'https://pokeapi.co/api/v2';

    public function getPokemonList(int $limit = 20, int $offset = 0): array
    {
        $cacheKey = "pokemon_list_{$limit}_{$offset}";
        
        return Cache::remember($cacheKey, 300, function () use ($limit, $offset) {
            $response = Http::get("{$this->baseUrl}/pokemon", [
                'limit' => $limit,
                'offset' => $offset,
            ]);

            if ($response->failed()) {
                throw new \Exception('Error al obtener la lista de pokémon');
            }

            $data = $response->json();
            
            // Obtener detalles de cada pokémon
            $pokemonList = [];
            foreach ($data['results'] as $pokemon) {
                $pokemonData = $this->getPokemonDetails($pokemon['url']);
                if ($pokemonData) {
                    $pokemonList[] = [
                        'id' => $pokemonData['id'],
                        'name' => $pokemonData['name'],
                        'image' => $pokemonData['sprites']['front_default'],
                        'types' => array_map(fn($type) => $type['type']['name'], $pokemonData['types']),
                    ];
                }
            }

            return [
                'results' => $pokemonList,
                'count' => $data['count'],
                'next' => $data['next'],
                'previous' => $data['previous'],
            ];
        });
    }

    public function getPokemon(string $id): array
    {
        $cacheKey = "pokemon_{$id}";
        
        return Cache::remember($cacheKey, 600, function () use ($id) {
            $response = Http::get("{$this->baseUrl}/pokemon/{$id}");

            if ($response->failed()) {
                throw new \Exception('Pokémon no encontrado');
            }

            $pokemon = $response->json();

            // Obtener información de la especie para la descripción
            $description = $this->getPokemonDescription($pokemon['species']['url']);

            return [
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
            ];
        });
    }

    public function searchPokemon(string $query): array
    {
        try {
            $pokemon = $this->getPokemon(strtolower($query));
            return [
                'results' => [$pokemon]
            ];
        } catch (\Exception $e) {
            return ['results' => []];
        }
    }

    private function getPokemonDetails(string $url): ?array
    {
        $response = Http::get($url);
        return $response->successful() ? $response->json() : null;
    }

    private function getPokemonDescription(string $speciesUrl): string
    {
        $response = Http::get($speciesUrl);
        
        if ($response->failed()) {
            return '';
        }

        $species = $response->json();
        $flavorTexts = collect($species['flavor_text_entries'])
            ->where('language.name', 'es')
            ->first();
        
        if (!$flavorTexts) {
            $flavorTexts = collect($species['flavor_text_entries'])
                ->where('language.name', 'en')
                ->first();
        }
        
        return $flavorTexts ? str_replace(["\n", "\f"], ' ', $flavorTexts['flavor_text']) : '';
    }
}