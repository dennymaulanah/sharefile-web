<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;

Route::get('/', function () {
    return view('pages.home');
});

Route::get('/data-file', [DocumentController::class, 'index']);
Route::post('/data-file/upload', [DocumentController::class, 'upload']);
Route::post('/data-file/folder', [DocumentController::class, 'createFolder']);
Route::put('/data-file/rename/{id}', [DocumentController::class, 'rename']);
Route::put('/data-file/move/{id}', [DocumentController::class, 'move']);
Route::get('/data-file/folder-download/{id}', [DocumentController::class, 'downloadFolder']);
Route::post('/data-file/create-web-doc', [DocumentController::class, 'createWebDoc']);
Route::get('/data-file/editor/{id}', [DocumentController::class, 'editor']);
Route::put('/data-file/editor/{id}', [DocumentController::class, 'updateWebDoc']);
Route::get('/data-file/download/{id}', [DocumentController::class, 'download']);
Route::delete('/data-file/{id}', [DocumentController::class, 'destroy']);
