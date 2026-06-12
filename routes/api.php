<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BiteshipWebhookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/midtrans/callback', [App\Http\Controllers\MidtransController::class, 'callback']);
Route::post('/webhooks/biteship', [BiteshipWebhookController::class, 'handle'])->name('webhooks.biteship');
