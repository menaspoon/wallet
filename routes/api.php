<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransferControler;
use App\Http\Controllers\BankWebhookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');




Route::post("transfers", [TransferControler::class, "store"]);
Route::post(
    '/webhooks/{bank}/{clint:internal_reference}',
    [BankWebhookController::class, 'store']
);