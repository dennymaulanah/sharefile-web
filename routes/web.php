<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\WebdavController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

// Admin Authentication
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');
Route::get('/admin/logout', [AuthController::class, 'logout']);

// Protected Admin Dashboard & Management
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/files', [AdminController::class, 'files'])->name('admin.files');
    Route::delete('/files/{id}', [AdminController::class, 'destroyFile'])->name('admin.files.destroy');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/users', [AdminController::class, 'createUser'])->name('admin.users.create');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::post('/profile', [AdminController::class, 'updateProfile'])->name('admin.profile.update');
    Route::post('/settings/sharefile-password', [AdminController::class, 'updateSharefilePassword'])->name('admin.settings.sharefile_password');
    Route::get('/trash', [AdminController::class, 'trash'])->name('admin.trash');
    Route::post('/trash/{id}/restore', [AdminController::class, 'restoreFile'])->name('admin.trash.restore');
    Route::delete('/trash/{id}/force', [AdminController::class, 'forceDeleteFile'])->name('admin.trash.force');
    Route::delete('/trash/empty', [AdminController::class, 'emptyTrash'])->name('admin.trash.empty');
});

Route::get('/', function () {
    return view('pages.home');
});

// WebDAV handler for Microsoft Word, Excel, and other WebDAV clients
Route::match(
    ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS', 'HEAD', 'PROPFIND', 'PROPPATCH', 'LOCK', 'UNLOCK'],
    '/webdav/{path?}',
    [WebdavController::class, 'handle']
)->where('path', '.*');

foreach (['/data-File', '/data-file'] as $prefix) {
    Route::prefix($prefix)->group(function () {
        Route::get('/', [DocumentController::class, 'index']);
        Route::post('/unlock', [DocumentController::class, 'unlock'])->name('sharefile.unlock');
        Route::post('/lock', [DocumentController::class, 'lock'])->name('sharefile.lock');
        Route::post('/ping-activity', [DocumentController::class, 'pingActivity']);

        // Operations protected by ShareFile password
        Route::middleware([\App\Http\Middleware\EnsureShareFileUnlocked::class])->group(function () {
            Route::post('/upload', [DocumentController::class, 'upload']);
            Route::post('/replace/{id}', [DocumentController::class, 'replaceFile']);
            Route::post('/folder', [DocumentController::class, 'createFolder']);
            Route::put('/rename/{id}', [DocumentController::class, 'rename']);
            Route::put('/move/{id}', [DocumentController::class, 'move']);
            Route::post('/batch-move', [DocumentController::class, 'batchMove'])->name('sharefile.batch-move');
            Route::post('/batch-delete', [DocumentController::class, 'batchDestroy'])->name('sharefile.batch-delete');
            Route::get('/folder-download/{id}', [DocumentController::class, 'downloadFolder']);
            Route::post('/create-web-doc', [DocumentController::class, 'createWebDoc']);
            Route::get('/editor/{id}', [DocumentController::class, 'editor']);
            Route::put('/editor/{id}', [DocumentController::class, 'updateWebDoc']);
            Route::get('/download/{id}', [DocumentController::class, 'download']);
            Route::get('/download-drive-bat', [DocumentController::class, 'downloadDriveBat']);
            Route::delete('/{id}', [DocumentController::class, 'destroy']);
        });
    });
}

