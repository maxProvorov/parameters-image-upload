<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParameterController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/parameters', [ParameterController::class, 'index'])->name('parameters.index');
Route::post('/parameters/{id}/images', [ParameterController::class, 'storeImages']);
Route::delete('/parameters/{id}/images/{imageName}', [ParameterController::class, 'deleteImage']);
Route::get('/api/parameters', [ParameterController::class, 'getParameters']);
