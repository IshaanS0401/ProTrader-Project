@extends('layouts.app')

@section('title', 'About')

@section('content')
<div class="bg-white shadow rounded p-8 max-w-3xl mx-auto space-y-6">
    <h1 class="text-4xl font-bold text-gray-800">📘 About This Project</h1>
    
    <p class="text-gray-700 text-lg leading-relaxed">
        ProTrader is a modern stock forecasting tool that leverages an <span class="font-semibold">LSTM (Long Short-Term Memory)</span> neural network to predict future stock prices based on historical market data whilst also educating novice traders on the way of the market!
    </p>

    <p class="text-gray-700 text-lg leading-relaxed">
        The backend is powered by <span class="font-semibold">FastAPI (Python)</span> for efficient model training and predictions, while the frontend uses <span class="font-semibold">Laravel (PHP)</span> and <span class="font-semibold">Chart.js</span> for dynamic and responsive data visualisations.
    </p>

    <p class="text-gray-700 text-lg leading-relaxed">
        On the prediction page, you can:
    </p>
    <ul class="list-disc list-inside text-gray-600 text-base space-y-1">
        <li>Train models for any valid stock ticker (e.g., AAPL, TSLA)</li>
        <li>Predict stock prices for 1, 5, or 10 days into the future</li>
        <li>Visualise predictions alongside historical prices</li>
        <li>Explore technical indicators like <span class="italic">RSI</span> and <span class="italic">MACD</span></li>
    </ul>

    <p class="text-gray-600 text-base">
        Whether you're learning about machine learning or exploring market trends, this tool is built for you.
    </p>

    <div class="pt-4">
        <a href="/stock" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg shadow hover:bg-blue-700 transition">
            🚀 Try the Predictor
        </a>
    </div>
</div>
@endsection
