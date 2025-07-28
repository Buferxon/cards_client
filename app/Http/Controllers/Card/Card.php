<?php

namespace App\Http\Controllers\Card;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Card extends Controller
{
    public function store(){
        try {
           
        } catch (\Exception $e) {
            // Manejo de excepciones
            return response()->json([
                'error' => 'Error al almacenar la tarjeta',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
