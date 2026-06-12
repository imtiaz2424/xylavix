<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('/chatbot', [App\Http\Controllers\ChatbotController::class, 'chat'])->name('chatbot')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::get('/sslcommerz/process', [Mmrtonmoybd\Sslcommerz\Http\Controllers\SslCommerzPaymentController::class, 'index'])->name('sslcommerz.process');