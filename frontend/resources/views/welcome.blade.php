@extends('layouts.app')

@section('title', 'Welcome to ProTrader')

@section('content')
<div class="bg-gradient-to-br from-blue-50 via-white to-gray-100 py-20 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto text-center space-y-8">
        
        <!-- Logo and App Name -->
        <div class="flex items-center justify-center space-x-3">
            <div class="w-16 h-16 rounded-full bg-blue-600 text-white text-2xl font-bold flex items-center justify-center shadow-lg">
                PT
            </div>
            <span class="text-3xl font-extrabold text-gray-800 tracking-tight">ProTrader</span>
        </div>

        <!-- Headline -->
        <h1 class="text-5xl font-extrabold text-gray-900 leading-tight">
            Predict the Future of Stocks 📊
        </h1>

        <!-- Subheading -->
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Empower your trading with real-time machine learning. Train models. Predict prices. Visualise technical indicators — all in seconds.
        </p>

        <!-- Call to Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/stock" class="inline-block px-8 py-3 bg-blue-600 text-white text-lg font-semibold rounded-lg shadow hover:bg-blue-700 transition-all duration-200">
                🚀 Start Predicting
            </a>
            <a href="/about" class="inline-block px-8 py-3 bg-white text-gray-800 text-lg font-semibold rounded-lg border border-gray-300 hover:bg-gray-100 transition-all duration-200">
                ℹ️ Learn More
            </a>
        </div>
    </div>
</div>
@endsection
