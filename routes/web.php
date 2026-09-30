<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AppointmentController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.home')->name('home');
Route::view('/about-dr-rajat-goel', 'pages.about')->name('about');
Route::view('/ent-services', 'pages.services')->name('services');
Route::view('/facilities', 'pages.facilities')->name('facilities');
Route::view('/gallery', 'pages.gallery')->name('gallery');
Route::view('/contact', 'pages.contact')->name('contact');

Route::permanentRedirect('/patient-information', '/gallery');

Route::post('/appointments', [AppointmentController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('appointments.store');

Route::get('/sitemap.xml', fn () => response()
    ->view('sitemap', ['routes' => ['home', 'about', 'services', 'facilities', 'gallery', 'contact']])
    ->header('Content-Type', 'application/xml'))
    ->name('sitemap');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [Admin\AuthController::class, 'create'])->name('login');
        Route::post('login', [Admin\AuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
    });

    Route::middleware('auth')->group(function () {
        Route::post('logout', [Admin\AuthController::class, 'destroy'])->name('logout');

        Route::get('/', Admin\DashboardController::class)->name('dashboard');

        Route::get('content/{screen}', [Admin\ContentController::class, 'edit'])->name('content.edit');
        Route::put('content/{screen}', [Admin\ContentController::class, 'update'])->name('content.update');
        Route::delete('content/{screen}', [Admin\ContentController::class, 'destroy'])->name('content.reset');

        Route::get('appointments', [Admin\AppointmentController::class, 'index'])->name('appointments.index');
        Route::patch('appointments/{appointment}', [Admin\AppointmentController::class, 'update'])->name('appointments.update');
        Route::delete('appointments/{appointment}', [Admin\AppointmentController::class, 'destroy'])->name('appointments.destroy');

        Route::get('account', [Admin\AccountController::class, 'edit'])->name('account.edit');
        Route::put('account', [Admin\AccountController::class, 'update'])->name('account.update');
    });
});
