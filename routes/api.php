<?php

use App\Http\Controllers\BalanceTransferController;
use App\Http\Controllers\CommitmentController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth')->group(function() {
    Route::get('{account}/transactions', [TransactionController::class, 'index'])->name('api.transaction.index');

    Route::post('{account}/transactions', [TransactionController::class, 'store'])->name('api.transaction.store');

    Route::get('{account}/commitments', [CommitmentController::class, 'index'])->name('api.commitment.index');

    Route::post('/accounts/transfer', [BalanceTransferController::class, 'store']);
});
