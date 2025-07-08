@extends('layouts.app') 

@section('title', 'Learn About Charts & Indicators') 

@push('head') 
   
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" /> --}}
    
@endpush

@section('content')
    {{-- Assuming app.blade.php provides the outer container/padding --}}

    <header class="text-center mb-12 md:mb-16">
        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-4">
            Chart & Indicator Guide
        </h1>
        <p class="text-lg md:text-xl text-gray-600 max-w-3xl mx-auto">
            Understand the tools available to analyze stock performance and make more informed decisions.
        </p>
    </header>

    {{-- Candlestick Chart --}}
    <section class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 mb-10 transition-shadow duration-300 hover:shadow-2xl" id="learn-candlestick">
        {{-- Header with direct styles --}}
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100 flex items-center space-x-4">
            {{-- Replaced Font Awesome with Emoji --}}
            <div class="text-3xl w-10 h-10 flex items-center justify-center rounded-lg bg-gray-100 text-gray-600 shadow-sm">
                🕯️
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Candlestick Chart</h2>
        </div>
        {{-- Body content remains the same --}}
        <div class="p-6 md:p-8 space-y-5 text-gray-700 leading-relaxed">
            <p>Candlestick charts are a staple of financial analysis, showing price movement over a specific period (e.g., one day). Each "candle" visualises four key prices:</p>
            <ul class="space-y-2 pl-5 list-disc list-inside">
                <li><strong class="font-semibold text-gray-800">Open:</strong> The price at the start of the period.</li>
                <li><strong class="font-semibold text-gray-800">High:</strong> The highest price reached during the period.</li>
                <li><strong class="font-semibold text-gray-800">Low:</strong> The lowest price reached during the period.</li>
                <li><strong class="font-semibold text-gray-800">Close:</strong> The price at the end of the period.</li>
            </ul>
            <h4 class="text-lg font-semibold text-gray-900 mt-5 mb-2 border-b border-gray-200 pb-1">How to Read It</h4>
            <ul class="space-y-2 pl-5 list-disc list-inside">
                 <li><strong class="font-semibold text-gray-800">Body:</strong> The wide rectangle between the Open and Close. A green or hollow body means the Close was higher than the Open (price up). A red of filled body means the Close was lower than the Open (price down).</li>
                 <li><strong class="font-semibold text-gray-800">Wicks (Shadows):</strong> Thin lines extending from the body. The top wick reaches the High price, the bottom wick reaches the Low price.</li>
                 <li><strong class="font-semibold text-gray-800">Interpretation:</strong> Long bodies indicate strong buying or selling pressure. Long wicks suggest significant price fluctuation during the period. Various candle shapes and sequences form patterns used in technical analysis.</li>
            </ul>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                 <div>
                     <h5 class="text-md font-semibold mb-2 flex items-center gap-2 text-green-700">✅ Pros</h5>
                     <ul class="space-y-1 text-sm list-none pl-0"> {{-- Removed list style --}}
                         <li class="flex items-start gap-2"><span class="text-green-500 pt-1">➕</span> Rich OHLC information per period.</li>
                         <li class="flex items-start gap-2"><span class="text-green-500 pt-1">➕</span> Excellent visualisation of volatility.</li>
                         <li class="flex items-start gap-2"><span class="text-green-500 pt-1">➕</span> Basis for many technical patterns.</li>
                         <li class="flex items-start gap-2"><span class="text-green-500 pt-1">➕</span> Widely used standard in finance.</li>
                     </ul>
                 </div>
                 <div>
                     <h5 class="text-md font-semibold mb-2 flex items-center gap-2 text-red-700">❌ Cons</h5>
                      <ul class="space-y-1 text-sm list-none pl-0">
                         <li class="flex items-start gap-2"><span class="text-red-500 pt-1">➖</span> Can appear complex initially.</li>
                         <li class="flex items-start gap-2"><span class="text-red-500 pt-1">➖</span> Pattern interpretation can be subjective.</li>
                         <li class="flex items-start gap-2"><span class="text-red-500 pt-1">➖</span> Patterns don't guarantee future outcomes.</li>
                     </ul>
                 </div>
             </div>
        </div>
    </section>

    {{-- Prediction Chart Section --}}
    <section class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 mb-10 transition-shadow duration-300 hover:shadow-2xl" id="learn-prediction">
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100 flex items-center space-x-4">
             {{-- Replaced Font Awesome with Emoji --}}
            <div class="text-3xl w-10 h-10 flex items-center justify-center rounded-lg bg-green-100 text-green-600 shadow-sm">
                📈
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Prediction Chart</h2>
        </div>
        {{-- Body content remains the same --}}
        <div class="p-6 md:p-8 space-y-5 text-gray-700 leading-relaxed">
             <p>This chart displays actual historical closing prices alongside the model's forecast for the *next* few trading days (based on your selection).</p>
             <h4 class="text-lg font-semibold text-gray-900 mt-5 mb-2 border-b border-gray-200 pb-1">How to Read It</h4>
             <ul class="space-y-2 pl-5 list-disc list-inside">
                 <li><strong class="font-semibold text-gray-800">Solid Line (History):</strong> Shows the actual closing prices up to the most recent available data point.</li>
                 <li><strong class="font-semibold text-gray-800">Dashed Line (Predicted):</strong> Shows the model's calculated prediction for future closing prices.</li>
             </ul>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                  <div>
                     <h5 class="text-md font-semibold mb-2 flex items-center gap-2 text-green-700">✅ Pros</h5>
                     <ul class="space-y-1 text-sm list-none pl-0">
                         <li class="flex items-start gap-2"><span class="text-green-500 pt-1">➕</span> Offers a potential future price trajectory based on the model.</li>
                         <li class="flex items-start gap-2"><span class="text-green-500 pt-1">➕</span> Easy visual comparison between forecast and recent past.</li>
                     </ul>
                 </div>
                 <div>
                     <h5 class="text-md font-semibold mb-2 flex items-center gap-2 text-red-700">❌ Cons</h5>
                      <ul class="space-y-1 text-sm list-none pl-0">
                         <li class="flex items-start gap-2"><span class="text-red-500 pt-1">➖</span> <strong Forecast, not a guarantee.</strong> Market conditions change.</li>
                         <li class="flex items-start gap-2"><span class="text-red-500 pt-1">➖</span> Accuracy depends on model, data, and market stability.</li>
                         <li class="flex items-start gap-2"><span class="text-red-500 pt-1">➖</span> Model learns from past data; may miss impact of new events.</li>
                     </ul>
                 </div>
             </div>
        </div>
    </section>

    {{-- Historical Comparison Chart --}}
    <section class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 mb-10 transition-shadow duration-300 hover:shadow-2xl" id="learn-comparison">
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100 flex items-center space-x-4">
              {{-- Replaced Font Awesome with Emoji --}}
             <div class="text-3xl w-10 h-10 flex items-center justify-center rounded-lg bg-orange-100 text-orange-600 shadow-sm">
                 🔍
             </div>
            <h2 class="text-2xl font-bold text-gray-800">Historical Performance Comparison</h2>
        </div>
         {{-- Body content remains the same --}}
        <div class="p-6 md:p-8 space-y-5 text-gray-700 leading-relaxed">
             <p>This chart evaluates the model's past accuracy by comparing its historical predictions against the actual prices for the same period.</p>
             <h4 class="text-lg font-semibold text-gray-900 mt-5 mb-2 border-b border-gray-200 pb-1">How to Read It</h4>
             <ul class="space-y-2 pl-5 list-disc list-inside">
                 <li><strong class="font-semibold text-gray-800">Solid Line (Actual):</strong> The real closing prices for the historical period shown.</li>
                 <li><strong class="font-semibold text-gray-800">Dashed Line (Predicted):</strong> What the model *would have* predicted for each past day, using only data available before that day.</li>
                 <li><strong class="font-semibold text-gray-800">Interpretation:</strong> The closer the two lines, the better the model's historical accuracy was for this stock during that time.</li>
             </ul>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                 <div>
                     <h5 class="text-md font-semibold mb-2 flex items-center gap-2 text-green-700">✅ Pros</h5>
                     <ul class="space-y-1 text-sm list-none pl-0">
                         <li class="flex items-start gap-2"><span class="text-green-500 pt-1">➕</span> Visual check of model's past accuracy on this stock.</li>
                         <li class="flex items-start gap-2"><span class="text-green-500 pt-1">➕</span> Helps build confidence (or caution) regarding the model.</li>
                         <li class="flex items-start gap-2"><span class="text-green-500 pt-1">➕</span> Can reveal periods of strong/weak model performance.</li>
                     </ul>
                 </div>
                 <div>
                     <h5 class="text-md font-semibold mb-2 flex items-center gap-2 text-red-700">❌ Cons</h5>
                      <ul class="space-y-1 text-sm list-none pl-0">
                         <li class="flex items-start gap-2"><span class="text-red-500 pt-1">➖</span> Past performance doesn't guarantee future results.</strong></li>
                         <li class="flex items-start gap-2"><span class="text-red-500 pt-1">➖</span> Only shows performance for the selected historical window.</li>
                     </ul>
                 </div>
             </div>
        </div>
    </section>

    {{-- Moving Averages (MA) --}}
    <section class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 mb-10 transition-shadow duration-300 hover:shadow-2xl" id="learn-ma">
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100 flex items-center space-x-4">
             {{-- Replaced Font Awesome with Emoji --}}
            <div class="text-3xl w-10 h-10 flex items-center justify-center rounded-lg bg-yellow-100 text-yellow-600 shadow-sm">
                〽️
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Moving Averages (MA)</h2>
        </div>
         {{-- Body content remains the same --}}
        <div class="p-6 md:p-8 space-y-5 text-gray-700 leading-relaxed">
             <p>Moving averages smooth price data to highlight the underlying trend direction over a defined period.</p>
             <h4 class="text-lg font-semibold text-gray-900 mt-5 mb-2 border-b border-gray-200 pb-1">How to Read It</h4>
             <ul class="space-y-2 pl-5 list-disc list-inside">
                 <li><strong class="font-semibold text-gray-800">MA 10 (Shorter-term):</strong> Average close price over the last 10 periods. Reacts quicker to price changes.</li>
                 <li><strong class="font-semibold text-gray-800">MA 50 (Longer-term):</strong> Average close price over the last 50 periods. Shows the broader trend.</li>
                 <li><strong class="font-semibold text-gray-800">Crossovers:</strong> When the short MA crosses above the long MA (Golden Cross), it can suggest bullish momentum. Crossing below (Death Cross) can suggest bearish momentum.</li>
                 <li><strong class="font-semibold text-gray-800">Trend Context:</strong> Price trading above MAs is often seen as positive; below is often negative.</li>
             </ul>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                 <div>
                     <h5 class="text-md font-semibold mb-2 flex items-center gap-2 text-green-700">✅ Pros</h5>
                     <ul class="space-y-1 text-sm list-none pl-0">
                         <li class="flex items-start gap-2"><span class="text-green-500 pt-1">➕</span> Simple, popular tool for trend identification.</li>
                         <li class="flex items-start gap-2"><span class="text-green-500 pt-1">➕</span> Filters out short-term price noise.</li>
                         <li class="flex items-start gap-2"><span class="text-green-500 pt-1">➕</span> Crossovers offer potential (but not guaranteed) signals.</li>
                     </ul>
                 </div>
                 <div>
                     <h5 class="text-md font-semibold mb-2 flex items-center gap-2 text-red-700">❌ Cons</h5>
                      <ul class="space-y-1 text-sm list-none pl-0">
                         <li class="flex items-start gap-2"><span class="text-red-500 pt-1">➖</span> Lagging:</strong> Reacts after price moves have occurred.</li>
                         <li class="flex items-start gap-2"><span class="text-red-500 pt-1">➖</span> Can give false signals ("whipsaws") in choppy markets.</li>
                         <li class="flex items-start gap-2"><span class="text-red-500 pt-1">➖</span> Choice of periods (10, 50, etc.) affects signals.</li>
                     </ul>
                 </div>
             </div>
        </div>
    </section>

    {{-- RSI --}}
    <section class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 mb-10 transition-shadow duration-300 hover:shadow-2xl" id="learn-rsi">
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100 flex items-center space-x-4">
             {{-- Replaced Font Awesome with Emoji --}}
            <div class="text-3xl w-10 h-10 flex items-center justify-center rounded-lg bg-purple-100 text-purple-600 shadow-sm">
                 📊
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Relative Strength Index (RSI)</h2>
        </div>
        {{-- Body content remains the same --}}
        <div class="p-6 md:p-8 space-y-5 text-gray-700 leading-relaxed">
              <p>RSI is a momentum oscillator measuring the speed and change of price movements, bounded between 0 and 100.</p>
             <h4 class="text-lg font-semibold text-gray-900 mt-5 mb-2 border-b border-gray-200 pb-1">How to Read It</h4>
             <ul class="space-y-2 pl-5 list-disc list-inside">
                 <li><strong class="font-semibold text-gray-800">RSI Line:</strong> Tracks the calculated RSI value.</li>
                 <li><strong class="font-semibold text-gray-800">Overbought (Above 70):</strong> Suggests the stock price may have risen too quickly and could be due for a pullback.</li>
                 <li><strong class="font-semibold text-gray-800">Oversold (Below 30):</strong> Suggests the stock price may have fallen too quickly and could be due for a bounce.</li>
                 <li><strong class="font-semibold text-gray-800">Divergence:</strong> When price makes a new high/low but RSI fails to confirm, it can signal weakening momentum and a potential reversal.</li>
             </ul>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                  <div>
                     <h5 class="text-md font-semibold mb-2 flex items-center gap-2 text-green-700">✅ Pros</h5>
                     <ul class="space-y-1 text-sm list-none pl-0">
                         <li class="flex items-start gap-2"><span class="text-green-500 pt-1">➕</span> Identifies potential overbought/oversold extremes.</li>
                         <li class="flex items-start gap-2"><span class="text-green-500 pt-1">➕</span> Divergence can signal potential trend changes.</li>
                         <li class="flex items-start gap-2"><span class="text-green-500 pt-1">➕</span> Clear 0-100 scale.</li>
                     </ul>
                 </div>
                 <div>
                     <h5 class="text-md font-semibold mb-2 flex items-center gap-2 text-red-700">❌ Cons</h5>
                      <ul class="space-y-1 text-sm list-none pl-0">
                         <li class="flex items-start gap-2"><span class="text-red-500 pt-1">➖</span> Can stay overbought/oversold during strong trends.</li>
                         <li class="flex items-start gap-2"><span class="text-red-500 pt-1">➖</span> Less reliable in non-trending (sideways) markets.</li>
                         <li class="flex items-start gap-2"><span class="text-red-500 pt-1">➖</span> Divergence signals are not foolproof.</li>
                     </ul>
                 </div>
             </div>
        </div>
    </section>

    {{-- MACD --}}
    <section class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 mb-10 transition-shadow duration-300 hover:shadow-2xl" id="learn-macd">
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100 flex items-center space-x-4">
              {{-- Replaced Font Awesome with Emoji --}}
             <div class="text-3xl w-10 h-10 flex items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 shadow-sm">
                 📉
             </div>
            <h2 class="text-2xl font-bold text-gray-800">MACD</h2>
        </div>
         {{-- Body content remains the same --}}
        <div class="p-6 md:p-8 space-y-5 text-gray-700 leading-relaxed">
             <p>MACD (Moving Average Convergence Divergence) shows the relationship between two moving averages of prices, indicating momentum.</p>
             <h4 class="text-lg font-semibold text-gray-900 mt-5 mb-2 border-b border-gray-200 pb-1">How to Read It</h4>
             <ul class="space-y-2 pl-5 list-disc list-inside">
                 <li><strong class="font-semibold text-gray-800">MACD Line:</strong> Difference between two EMAs (typically 12 & 26 periods).</li>
                 <li><strong class="font-semibold text-gray-800">Signal Line:</strong> An EMA (typically 9 periods) of the MACD Line itself.</li>
                 <li><strong class="font-semibold text-gray-800">Crossovers:</strong> MACD crossing above Signal is potentially bullish; crossing below is potentially bearish.</li>
                 <li><strong class="font-semibold text-gray-800">Centerline (Zero):</strong> MACD crossing above zero suggests increasing bullish momentum; below zero suggests increasing bearish momentum.</li>
             </ul>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                 <div>
                     <h5 class="text-md font-semibold mb-2 flex items-center gap-2 text-green-700">✅ Pros</h5>
                     <ul class="space-y-1 text-sm list-none pl-0">
                         <li class="flex items-start gap-2"><span class="text-green-500 pt-1">➕</span> Combines trend and momentum.</li>
                         <li class="flex items-start gap-2"><span class="text-green-500 pt-1">➕</span> Provides relatively clear crossover signals.</li>
                         <li class="flex items-start gap-2"><span class="text-green-500 pt-1">➕</span> Useful for gauging trend strength and direction changes.</li>
                     </ul>
                 </div>
                 <div>
                     <h5 class="text-md font-semibold mb-2 flex items-center gap-2 text-red-700">❌ Cons</h5>
                      <ul class="space-y-1 text-sm list-none pl-0">
                         <li class="flex items-start gap-2"><span class="text-red-500 pt-1">➖</span> Lagging:</strong> Signals appear after the move starts.</li>
                         <li class="flex items-start gap-2"><span class="text-red-500 pt-1">➖</span> Prone to false signals in sideways markets.</li>
                         <li class="flex items-start gap-2"><span class="text-red-500 pt-1">➖</span> Crossovers often need confirmation from other indicators.</li>
                     </ul>
                 </div>
             </div>
        </div>
    </section>

    {{-- OBV --}}
    <section class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 mb-10 transition-shadow duration-300 hover:shadow-2xl" id="learn-obv">
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100 flex items-center space-x-4">
              {{-- Replaced Font Awesome with Emoji --}}
             <div class="text-3xl w-10 h-10 flex items-center justify-center rounded-lg bg-teal-100 text-teal-600 shadow-sm">
                 💹
             </div>
            <h2 class="text-2xl font-bold text-gray-800">On-Balance Volume (OBV)</h2>
        </div>
         {{-- Body content remains the same --}}
        <div class="p-6 md:p-8 space-y-5 text-gray-700 leading-relaxed">
             <p>OBV uses trading volume to predict price changes. It accumulates volume on up-days and subtracts it on down-days.</p>
             <h4 class="text-lg font-semibold text-gray-900 mt-5 mb-2 border-b border-gray-200 pb-1">How to Read It</h4>
             <ul class="space-y-2 pl-5 list-disc list-inside">
                 <li><strong class="font-semibold text-gray-800">OBV Line:</strong> The running total of volume based on price closes.</li>
                 <li><strong class="font-semibold text-gray-800">Trend Confirmation:</strong> Ideally, OBV should rise in an uptrend and fall in a downtrend.</li>
                 <li><strong class="font-semibold text-gray-800">Divergence:</strong> If price makes a new high but OBV doesn't, it may signal underlying weakness. If price makes a new low but OBV doesn't, it may signal weakening selling pressure.</li>
                 <li><strong class="font-semibold text-gray-800">Focus on Direction:</strong> The absolute value isn't as important as the OBV line's trend and its relationship to the price trend.</li>
             </ul>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                 <div>
                     <h5 class="text-md font-semibold mb-2 flex items-center gap-2 text-green-700">✅ Pros</h5>
                     <ul class="space-y-1 text-sm list-none pl-0">
                         <li class="flex items-start gap-2"><span class="text-green-500 pt-1">➕</span> Can sometimes lead price movements.</li>
                         <li class="flex items-start gap-2"><span class="text-green-500 pt-1">➕</span> Helps confirm trend strength via volume flow.</li>
                         <li class="flex items-start gap-2"><span class="text-green-500 pt-1">➕</span> Divergences can offer early reversal warnings.</li>
                     </ul>
                 </div>
                 <div>
                     <h5 class="text-md font-semibold mb-2 flex items-center gap-2 text-red-700">❌ Cons</h5>
                      <ul class="space-y-1 text-sm list-none pl-0">
                         <li class="flex items-start gap-2"><span class="text-red-500 pt-1">➖</span> Can be volatile or "noisy".</li>
                         <li class="flex items-start gap-2"><span class="text-red-500 pt-1">➖</span> Large single-day volume spikes can distort the indicator.</li>
                         <li class="flex items-start gap-2"><span class="text-red-500 pt-1">➖</span> Doesn't consider the magnitude of price changes.</li>
                     </ul>
                 </div>
             </div>
        </div>
    </section>

    {{-- Volatility --}}
    <section class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 mb-10 transition-shadow duration-300 hover:shadow-2xl" id="learn-volatility">
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100 flex items-center space-x-4">
             {{-- Replaced Font Awesome with Emoji --}}
            <div class="text-3xl w-10 h-10 flex items-center justify-center rounded-lg bg-red-100 text-red-600 shadow-sm">
                ⚡
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Historical Volatility</h2>
        </div>
         {{-- Body content remains the same --}}
        <div class="p-6 md:p-8 space-y-5 text-gray-700 leading-relaxed">
             <p>Measures the degree of price fluctuation over a set period, often shown as the standard deviation of daily price returns.</p>
             <h4 class="text-lg font-semibold text-gray-900 mt-5 mb-2 border-b border-gray-200 pb-1">How to Read It</h4>
             <ul class="space-y-2 pl-5 list-disc list-inside">
                 <li><strong class="font-semibold text-gray-800">Volatility Line:</strong> Higher values mean larger price swings (more risk/uncertainty). Lower values mean smaller price swings (calmer market).</li>
                 <li><strong class="font-semibold text-gray-800">Cycles:</strong> Markets often cycle between periods of high and low volatility.</li>
             </ul>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                 <div>
                     <h5 class="text-md font-semibold mb-2 flex items-center gap-2 text-green-700">✅ Pros</h5>
                     <ul class="space-y-1 text-sm list-none pl-0">
                         <li class="flex items-start gap-2"><span class="text-green-500 pt-1">➕</span> Quantifies market risk or price dispersion.</li>
                         <li class="flex items-start gap-2"><span class="text-green-500 pt-1">➕</span> Helps identify potentially calm vs. choppy periods.</li>
                     </ul>
                 </div>
                 <div>
                     <h5 class="text-md font-semibold mb-2 flex items-center gap-2 text-red-700">❌ Cons</h5>
                      <ul class="space-y-1 text-sm list-none pl-0">
                         <li class="flex items-start gap-2"><span class="text-red-500 pt-1">➖</span> Doesn't indicate price direction.</strong> High volatility can happen in up or down trends.</li>
                         <li class="flex items-start gap-2"><span class="text-red-500 pt-1">➖</span> Lagging indicator based on past movements.</li>
                     </ul>
                 </div>
             </div>
        </div>
    </section>

    {{-- ROC --}}
    <section class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 mb-10 transition-shadow duration-300 hover:shadow-2xl" id="learn-roc">
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100 flex items-center space-x-4">
             {{-- Replaced Font Awesome with Emoji --}}
            <div class="text-3xl w-10 h-10 flex items-center justify-center rounded-lg bg-pink-100 text-pink-600 shadow-sm">
                ⏱️
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Rate of Change (ROC)</h2>
        </div>
         {{-- Body content remains the same --}}
        <div class="p-6 md:p-8 space-y-5 text-gray-700 leading-relaxed">
             <p>ROC measures the percentage change in price between the current price and the price 'n' periods ago (e.g., 5 days), indicating momentum.</p>
             <h4 class="text-lg font-semibold text-gray-900 mt-5 mb-2 border-b border-gray-200 pb-1">How to Read It</h4>
             <ul class="space-y-2 pl-5 list-disc list-inside">
                 <li><strong class="font-semibold text-gray-800">ROC Line:</strong> Shows the speed of price change.</li>
                 <li><strong class="font-semibold text-gray-800">Above Zero:</strong> Price is higher than 'n' periods ago (upward momentum).</li>
                 <li><strong class="font-semibold text-gray-800">Below Zero:</strong> Price is lower than 'n' periods ago (downward momentum).</li>
                 <li><strong class="font-semibold text-gray-800">Steepness:</strong> Indicates the strength of the momentum.</li>
             </ul>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                 <div>
                     <h5 class="text-md font-semibold mb-2 flex items-center gap-2 text-green-700">✅ Pros</h5>
                     <ul class="space-y-1 text-sm list-none pl-0">
                         <li class="flex items-start gap-2"><span class="text-green-500 pt-1">➕</span> Clearly shows momentum (speed of change).</li>
                         <li class="flex items-start gap-2"><span class="text-green-500 pt-1">➕</span> Can identify divergences.</li>
                         <li class="flex items-start gap-2"><span class="text-green-500 pt-1">➕</span> Simple interpretation around the zero line.</li>
                     </ul>
                 </div>
                 <div>
                     <h5 class="text-md font-semibold mb-2 flex items-center gap-2 text-red-700">❌ Cons</h5>
                      <ul class="space-y-1 text-sm list-none pl-0">
                         <li class="flex items-start gap-2"><span class="text-red-500 pt-1">➖</span> Can be erratic ("choppy"), especially with short periods.</li>
                         <li class="flex items-start gap-2"><span class="text-red-500 pt-1">➖</span> Sensitive to the chosen lookback period ('n').</li>
                         <li class="flex items-start gap-2"><span class="text-red-500 pt-1">➖</span> No fixed overbought/oversold levels like RSI.</li>
                     </ul>
                 </div>
             </div>
        </div>
    </section>


@endsection

@push('scripts')
{{-- No specific JS needed for this static page --}}
@endpush
