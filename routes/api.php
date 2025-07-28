<?php

use App\Http\Controllers\Card\CardController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    // Rutas de usuarios
    Route::apiResource('users', UserController::class);

    // Rutas adicionales si las necesitas
    Route::get('users/search/{term}', [UserController::class, 'search'])->name('users.search');





    //Consulta numero de tarjeta
    Route::get('cards/{crd_intsnr}', [CardController::class, 'getCard'])
        ->name('cards.get_number');
});
