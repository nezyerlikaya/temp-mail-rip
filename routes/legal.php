<?php

use App\Http\Controllers\LegalPageController;
use Illuminate\Support\Facades\Route;

Route::get('/legal/privacy-policy', [LegalPageController::class, 'show'])->name('legal.privacy_policy');
Route::get('/legal/terms-of-service', [LegalPageController::class, 'show'])->name('legal.terms_of_service');
Route::get('/legal/cookie-policy', [LegalPageController::class, 'show'])->name('legal.cookie_policy');
Route::get('/legal/acceptable-use-policy', [LegalPageController::class, 'show'])->name('legal.acceptable_use_policy');
