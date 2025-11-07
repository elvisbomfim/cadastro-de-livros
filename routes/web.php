<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RelatorioController;

Route::get('/', function () {
    return view('app');
});

Route::get('/app{any}', function () {
    return view('app');
})->where('any', '.*');

Route::prefix('relatorios')->group(function () {
    Route::get('/livros-por-categoria/pdf', [RelatorioController::class, 'livrosPorCategoria'])->name('relatorios.livros-por-categoria.pdf');
    Route::get('/livro/{id}/ficha/pdf', [RelatorioController::class, 'fichaDetalhadaLivro'])->name('relatorios.ficha-livro.pdf');
    Route::get('/por-autor/pdf', [RelatorioController::class, 'relatorioPorAutor'])->name('relatorios.por-autor.pdf');
    
    Route::get('/livros-por-categoria', [RelatorioController::class, 'livrosPorCategoriaJson'])->name('relatorios.livros-por-categoria');
    Route::get('/livro/{id}/ficha', [RelatorioController::class, 'fichaDetalhadaLivroJson'])->name('relatorios.ficha-livro');
    Route::get('/por-autor', [RelatorioController::class, 'relatorioPorAutorJson'])->name('relatorios.por-autor');
});
