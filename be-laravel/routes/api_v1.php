<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\v1\ReceiptController;

Route::get('/', [ReceiptController::class, 'index']);
Route::get('/receipts/filters', [ReceiptController::class, 'indexFiltered']);
Route::apiResource('receipts', ReceiptController::class);
Route::get('items', [ReceiptController::class, 'getItems']);
