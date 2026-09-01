<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;

Route::get('/', function () {
    return view('pages.home');
});

Route::get('/data-budidaya', [DocumentController::class, 'index']);
Route::post('/data-budidaya/upload', [DocumentController::class, 'upload']);
Route::post('/data-budidaya/folder', [DocumentController::class, 'createFolder']);
Route::put('/data-budidaya/rename/{id}', [DocumentController::class, 'rename']);
Route::put('/data-budidaya/move/{id}', [DocumentController::class, 'move']);
Route::get('/data-budidaya/folder-download/{id}', [DocumentController::class, 'downloadFolder']);
Route::post('/data-budidaya/create-web-doc', [DocumentController::class, 'createWebDoc']);
Route::get('/data-budidaya/editor/{id}', [DocumentController::class, 'editor']);
Route::put('/data-budidaya/editor/{id}', [DocumentController::class, 'updateWebDoc']);
Route::get('/data-budidaya/download/{id}', [DocumentController::class, 'download']);
Route::delete('/data-budidaya/{id}', [DocumentController::class, 'destroy']);
