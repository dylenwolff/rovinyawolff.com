<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/services', [HomeController::class, 'services'])->name('services');
Route::get('/work', [HomeController::class, 'work'])->name('work');
Route::get('/work/{project}', [HomeController::class, 'project'])->name('work.show');
Route::get('/contact', [HomeController::class, 'contactPage'])->name('contact');
Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');
Route::post('/contact', [HomeController::class, 'contact'])->middleware('throttle:5,1')->name('contact.store');
Route::get('/sitemap.xml', [HomeController::class, 'sitemap'])->name('sitemap');
