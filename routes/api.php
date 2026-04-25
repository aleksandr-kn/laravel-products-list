<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

// логин - возвращает Sanctum-токен, который фронтенд кладет в localStorage
Route::post('/login', [AuthController::class, 'login']);

// категории отдаем без пагинации - нужен полный список для выпадающего списка
Route::get('/categories', [CategoryController::class, 'index']);

// список и просмотр товаров - публичные, без авторизации
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);

// создание, обновление и удаление - только с токеном в заголовке Authorization
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::patch('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
});
