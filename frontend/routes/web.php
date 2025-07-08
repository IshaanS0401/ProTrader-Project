<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\StockController;

Route::view('/', 'welcome');
Route::view('/about', 'about');

Route::get('/learn', [StockController::class, 'learn'])->name('learn');
Route::get('/stock', [StockController::class, 'index']);
Route::post('/stock/train', [StockController::class, 'train']);
Route::post('/stock/predict', [StockController::class, 'predict']);
Route::post('/stock/history', [StockController::class, 'history']);
Route::post('/stock/indicators', [StockController::class, 'indicators']);
Route::post('/stock/predict-historical', [StockController::class, 'predictHistorical']);
Route::post('/stock/ohlc-history', [StockController::class, 'ohlcHistory']);

Route::get('/models', [StockController::class, 'listModels']);
Route::post('/models/delete', function (\Illuminate\Http\Request $request) {
    $ticker = strtoupper($request->input('ticker'));
    $path = base_path("models/{$ticker}_model.h5");

    if (file_exists($path)) {
        unlink($path);
        return redirect('/models')->with('success', "$ticker model deleted.");
    }

    return redirect('/models')->with('error', "$ticker model not found.");
});

Route::post('/api/stock/predict', function (\Illuminate\Http\Request $request) {
    $ticker = $request->input('ticker');
    $days = $request->input('days', 1); 

    $response = Http::post("http://host.docker.internal:8000/predict?days={$days}", [
        'ticker' => $ticker
    ]);

    return response()->json($response->json());
});