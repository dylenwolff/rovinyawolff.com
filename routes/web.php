<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController;
use App\Support\AdminResources;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/services', [HomeController::class, 'services'])->name('services');
Route::get('/work', [HomeController::class, 'work'])->name('work');
Route::get('/work/publications', [HomeController::class, 'publications'])->name('work.publications');
Route::get('/work/social-media', [HomeController::class, 'socialMedia'])->name('work.social');
Route::get('/work/{project}', [HomeController::class, 'project'])->name('work.show');
Route::get('/contact', [HomeController::class, 'contactPage'])->name('contact');
Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');
Route::post('/contact', [HomeController::class, 'contact'])->middleware('throttle:5,1')->name('contact.store');
Route::get('/sitemap.xml', [HomeController::class, 'sitemap'])->name('sitemap');

$adminResourcePattern = implode('|', array_keys(AdminResources::all()));

Route::domain(config('app.admin_domain', 'admin.rovinyawolff.com'))->name('admin.')->group(function () use ($adminResourcePattern) {
    Route::get('/login', [AuthController::class, 'show'])->name('login');
    Route::post('/login/code', [AuthController::class, 'send'])->middleware('throttle:6,1')->name('login.code');
    Route::post('/login/verify', [AuthController::class, 'verify'])->middleware('throttle:12,1')->name('login.verify');
    Route::middleware('auth')->group(function () use ($adminResourcePattern) {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::post('/outreach-emails/{record}/send', [AdminController::class, 'sendEmail'])->name('outreach.send');
        Route::get('/{resource}', [AdminController::class, 'index'])->where('resource', $adminResourcePattern)->name('resources.index');
        Route::get('/{resource}/create', [AdminController::class, 'create'])->where('resource', $adminResourcePattern)->name('resources.create');
        Route::post('/{resource}', [AdminController::class, 'store'])->where('resource', $adminResourcePattern)->name('resources.store');
        Route::get('/{resource}/{record}/edit', [AdminController::class, 'edit'])->where('resource', $adminResourcePattern)->name('resources.edit');
        Route::put('/{resource}/{record}', [AdminController::class, 'update'])->where('resource', $adminResourcePattern)->name('resources.update');
        Route::delete('/{resource}/{record}', [AdminController::class, 'destroy'])->where('resource', $adminResourcePattern)->name('resources.destroy');
    });
});
