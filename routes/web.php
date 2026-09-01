<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;

Route::get('/', function () {
    return view('pages.home');
});

Route::get('/data-File', [DocumentController::class, 'index']);
Route::post('/data-File/upload', [DocumentController::class, 'upload']);
Route::post('/data-File/folder', [DocumentController::class, 'createFolder']);
Route::put('/data-File/rename/{id}', [DocumentController::class, 'rename']);
Route::put('/data-File/move/{id}', [DocumentController::class, 'move']);
Route::get('/data-File/folder-download/{id}', [DocumentController::class, 'downloadFolder']);
Route::post('/data-File/create-web-doc', [DocumentController::class, 'createWebDoc']);
Route::get('/data-File/editor/{id}', [DocumentController::class, 'editor']);
Route::put('/data-File/editor/{id}', [DocumentController::class, 'updateWebDoc']);
Route::get('/data-File/download/{id}', [DocumentController::class, 'download']);
Route::delete('/data-File/{id}', [DocumentController::class, 'destroy']);
