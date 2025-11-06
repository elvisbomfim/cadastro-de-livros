<?php

use App\Http\Controllers\Api\LivroController;
use App\Http\Controllers\Api\AutorController;
use App\Http\Controllers\Api\AssuntoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::apiResource('livros', LivroController::class);
Route::apiResource('autores', AutorController::class);
Route::apiResource('assuntos', AssuntoController::class);