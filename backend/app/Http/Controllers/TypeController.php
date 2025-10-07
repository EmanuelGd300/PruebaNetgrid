<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class TypeController extends Controller
{
    private $baseUrl = 'https://pokeapi.co/api/v2';

    public function index()
    {
        $response = Http::get("{$this->baseUrl}/type");

        if ($response->failed()) {
            return response()->json(['error' => 'Error al obtener tipos'], 500);
        }

        $data = $response->json();
        
        return response()->json([
            'results' => collect($data['results'])
                ->filter(fn($type) => !in_array($type['name'], ['unknown', 'shadow']))
                ->values()
        ]);
    }
}