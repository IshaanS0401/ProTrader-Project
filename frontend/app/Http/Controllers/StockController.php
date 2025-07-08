<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View; 

class StockController extends Controller
{
    public function index(): View
    {
        return view('stock');
    }

    public function learn(): View
    {
        return view('learn');
    }

    public function train(Request $request)
    {
        $ticker = $request->input('ticker');
        $apiUrl = config('services.fastapi.url', 'http://host.docker.internal:8000');
        $ticker = strtoupper(trim($ticker ?? ''));

        if (!$ticker) {
            return back()->with('trainResult', 'Error: Ticker symbol is required.')->with('trainStatus', 'error');
        }

        $response = Http::timeout(300)->post($apiUrl . '/train?ticker=' . urlencode($ticker));

        if ($response->successful()) {
            $responseData = $response->json();
            $message = $responseData['message'] ?? 'Training initiated successfully.';
          
            return back()->with('trainResult', $message)->with('trainStatus', 'success');

        } else {
            $errorData = $response->json();
            $detail = $errorData['detail'] ?? ('Training failed (' . $response->status() . '). Check the ML service.');
            \Log::error("Training Error for $ticker: Status " . $response->status() . " - " . ($errorData ? json_encode($errorData) : $response->body()));
            
            return back()->with('trainResult', 'Error: ' . $detail)->with('trainStatus', 'error');
        }
    }

 
    public function predict(Request $request)
    {
        $ticker = $request->input('ticker');
        $days = $request->input('days', 1);
        $apiUrl = config('services.fastapi.url', 'http://host.docker.internal:8000');

        $response = Http::post($apiUrl . "/predict?days=" . (int)$days, [
            'ticker' => $ticker
        ]);

        $data = $response->json();

        if ($response->failed() || isset($data['detail'])) {
            $message = $data['detail'] ?? 'Prediction failed. Try training the model first.';
             \Log::error("Prediction Error (Form) for $ticker: Status " . $response->status() . " - " . $message);
            return back()->with('predictionError', $message);
        }
        return back()->with('predictionData', $data);
    }

    public function history(Request $request)
    {
        $ticker = $request->input('ticker');
        $days = $request->input('days', 60);
        $apiUrl = config('services.fastapi.url', 'http://host.docker.internal:8000');

        if (!$ticker) { return response()->json(['error' => 'Ticker is required'], 400); }
        $ticker = strtoupper(trim($ticker));
        $response = Http::timeout(120)->get($apiUrl . '/history', ['ticker' => $ticker, 'days' => $days]);
        return response()->json($response->json(), $response->status());
    }

     public function ohlcHistory(Request $request)
     {
         $ticker = $request->input('ticker');
         $days = $request->input('days', 100);
         $apiUrl = config('services.fastapi.url', 'http://host.docker.internal:8000');

         if (!$ticker) { return response()->json(['error' => 'Ticker is required'], 400); }
         $ticker = strtoupper(trim($ticker));
         $response = Http::timeout(120)->get($apiUrl . '/ohlc_history', ['ticker' => $ticker, 'days' => $days]);
         return response()->json($response->json(), $response->status());
     }

     public function indicators(Request $request)
     {
         $ticker = $request->input('ticker');
         $days = $request->input('days', 100);
         $apiUrl = config('services.fastapi.url', 'http://host.docker.internal:8000');

         if (!$ticker) { return response()->json(['error' => 'Ticker is required'], 400); }
         $ticker = strtoupper(trim($ticker));
         $response = Http::timeout(120)->get($apiUrl . "/indicators", ['ticker' => $ticker, 'days' => $days]);
         return response()->json($response->json(), $response->status());
     }

    public function predictHistorical(Request $request)
    {
        $ticker = $request->input('ticker');
        $days = $request->input('days', 60);
        $apiUrl = config('services.fastapi.url', 'http://host.docker.internal:8000');

        if (!$ticker) { return response()->json(['error' => 'Ticker is required'], 400); }
         $ticker = strtoupper(trim($ticker));
        $response = Http::timeout(300)->post($apiUrl . '/predict_historical?days=' . $days, ['ticker' => $ticker]);
        return response()->json($response->json(), $response->status());
    }

    public function listModels(): View
    {
        $models = [];
        $modelDir = config('services.model_dir', base_path('models'));
        if (!is_dir($modelDir)) { \Log::warning("Model directory not found: " . $modelDir);
        } else {
            $files = glob($modelDir . '/*_model.h5');
            if ($files !== false) {
                foreach ($files as $file) {
                    $basename = basename($file);
                    if (str_ends_with($basename, '_model.h5')) {
                         $ticker = strtoupper(substr($basename, 0, -10));
                         if (!empty($ticker) && is_readable($file)) {
                             $models[] = ['ticker' => $ticker, 'trained_at' => date("Y-m-d H:i", filemtime($file))];
                         } elseif (!is_readable($file)) { \Log::warning("Model file not readable: " . $file); }
                    }
                }
            } else { \Log::error("Failed to read model directory: " . $modelDir); }
        }
        usort($models, fn($a, $b) => $a['ticker'] <=> $b['ticker']);
        return view('models', compact('models'));
    }
}
