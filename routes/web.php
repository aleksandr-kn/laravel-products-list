<?php

use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// публичные страницы
Route::get('/', [PublicController::class, 'index']);
Route::get('/products/{product}', [PublicController::class, 'show']);

// страница логина - рендерим Vue-компонент через Inertia
Route::get('/login', fn () => Inertia::render('Auth/Login'));

// страницы админки - защита на уровне фронтенда через useAuth
Route::prefix('admin')->group(function () {
    Route::get('/products', [AdminProductController::class, 'index']);
    Route::get('/products/create', [AdminProductController::class, 'create']);
    Route::get('/products/{product}/edit', [AdminProductController::class, 'edit']);
});
