@extends('layouts.app')

@section('title', 'Stock Price Predictor')

{{-- Add CSRF Token meta tag --}}
@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="space-y-8">
    {{-- Train Form --}}
    <div class="bg-white shadow rounded p-6">
        <h1 class="text-3xl font-semibold text-gray-800 mb-4">📈 Stock Price Predictor</h1>
        <form method="POST" action="/stock/train" class="space-y-4">
            @csrf
            <div class="flex gap-2">
                <div class="flex-1">
                    <input list="sp500-tickers" type="text" name="ticker" class="w-full border border-gray-300 rounded px-4 py-2" placeholder="Search Ticker (e.g., AAPL)" required>
                    {{-- Datalist for tickers --}}
                    <datalist id="sp500-tickers">
                        <option value="AAPL">Apple Inc.</option>
                        <option value="MSFT">Microsoft Corporation</option>
                        <option value="AMZN">Amazon.com Inc.</option>
                        <option value="GOOGL">Alphabet Inc. (Class A)</option>
                        <option value="GOOG">Alphabet Inc. (Class C)</option>
                        <option value="META">Meta Platforms Inc.</option>
                        <option value="TSLA">Tesla Inc.</option>
                        <option value="BRK.B">Berkshire Hathaway Inc.</option>
                        <option value="JNJ">Johnson & Johnson</option>
                        <option value="V">Visa Inc.</option>
                        <option value="JPM">JPMorgan Chase & Co.</option>
                        <option value="WMT">Walmart Inc.</option>
                        <option value="PG">Procter & Gamble</option>
                        <option value="MA">Mastercard Incorporated</option>
                        <option value="UNH">UnitedHealth Group</option>
                        <option value="HD">Home Depot Inc.</option>
                        <option value="NVDA">NVIDIA Corporation</option>
                        <option value="PFE">Pfizer Inc.</option>
                        <option value="BAC">Bank of America Corp</option>
                        <option value="DIS">Walt Disney Company</option>
                        <option value="XOM">Exxon Mobil Corporation</option>
                        <option value="VZ">Verizon Communications</option>
                        <option value="ADBE">Adobe Inc.</option>
                        <option value="CRM">Salesforce Inc.</option>
                        <option value="CSCO">Cisco Systems Inc.</option>
                        <option value="KO">Coca-Cola Company</option>
                        <option value="PEP">PepsiCo Inc.</option>
                        <option value="NFLX">Netflix Inc.</option>
                        <option value="CMCSA">Comcast Corporation</option>
                        <option value="ABT">Abbott Laboratories</option>
                        <option value="TMO">Thermo Fisher Scientific</option>
                        <option value="ABBV">AbbVie Inc.</option>
                        <option value="DHR">Danaher Corporation</option>
                        <option value="ACN">Accenture plc</option>
                        <option value="NKE">Nike Inc.</option>
                        <option value="T">AT&T Inc.</option>
                        <option value="PM">Philip Morris International</option>
                        <option value="LIN">Linde plc</option>
                        <option value="AVGO">Broadcom Inc.</option>
                        <option value="ORCL">Oracle Corporation</option>
                        <option value="LLY">Eli Lilly and Company</option>
                        <option value="CVX">Chevron Corporation</option>
                        <option value="MRK">Merck & Co. Inc.</option>
                        <option value="BMY">Bristol-Myers Squibb</option>
                        <option value="AMD">Advanced Micro Devices</option>
                        <option value="QCOM">QUALCOMM Incorporated</option>
                        <option value="INTU">Intuit Inc.</option>
                        <option value="HON">Honeywell International</option>
                        <option value="AMGN">Amgen Inc.</option>
                        <option value="SBUX">Starbucks Corporation</option>
                    </datalist>
                </div>
                <button class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Train</button>
            </div>
        </form>

        {{-- Display Train Result --}}
        @if(session('trainResult'))
            <div class="mt-4 bg-blue-100 text-blue-800 px-4 py-2 rounded">
                {{ session('trainResult') }}
            </div>
        @endif
    </div>

    {{-- Predict Form --}}
    <div class="bg-white shadow rounded p-6">
        <form method="POST" id="predictionForm" action="/stock/predict" class="space-y-4">
            @csrf
            <div class="flex gap-2 items-center">
                <div class="flex-1">
                    <input list="sp500-tickers" type="text" name="ticker" id="tickerInput" class="w-full border border-gray-300 rounded px-4 py-2" placeholder="Search Ticker (e.g., AAPL)" required>
                </div>
                <select name="days" id="forecastDays" class="border border-gray-300 rounded px-3 py-2">
                    <option value="1">1 Day</option>
                    <option value="5">5 Days</option>
                    <option value="10">10 Days</option>
                </select>
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Predict</button>
            </div>
        </form>

        {{-- Display Prediction Result --}}
        <div id="predictionValue" class="mt-4 hidden px-4 py-3 rounded bg-gray-100 text-gray-800 border border-gray-300">
            <strong>Predicted Prices:</strong> <span id="predictedValue"></span>
        </div>
    </div>

    {{-- Toggle Buttons Container --}}
    <div class="bg-white shadow rounded p-6 flex flex-wrap gap-4">
        <button id="toggleCandlestick" class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800">
            <span class="show-text">🕯️ Show Candlestick</span>
            <span class="hide-text hidden">🕯️ Hide Candlestick</span>
        </button>
        <button id="togglePrediction" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            <span class="show-text">📈 Show Prediction Chart</span>
            <span class="hide-text hidden">📈 Hide Prediction Chart</span>
        </button>
        <button id="toggleRSI" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
            <span class="show-text">📊 Show RSI</span>
            <span class="hide-text hidden">📊 Hide RSI</span>
        </button>
        <button id="toggleMACD" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            <span class="show-text">📉 Show MACD</span>
            <span class="hide-text hidden">📉 Hide MACD</span>
        </button>
        <button id="toggleOBV" class="bg-teal-600 text-white px-4 py-2 rounded hover:bg-teal-700">
            <span class="show-text">💹 Show OBV</span>
            <span class="hide-text hidden">💹 Hide OBV</span>
        </button>
        <button id="toggleMA" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
            <span class="show-text">〽️ Show MA (10/50)</span>
            <span class="hide-text hidden">〽️ Hide MA (10/50)</span>
        </button>
        <button id="toggleVolatility" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
            <span class="show-text">⚡ Show Volatility</span>
            <span class="hide-text hidden">⚡ Hide Volatility</span>
        </button>
        <button id="toggleROC" class="bg-pink-600 text-white px-4 py-2 rounded hover:bg-pink-700">
            <span class="show-text">⏱️ Show ROC (5-day)</span>
            <span class="hide-text hidden">⏱️ Hide ROC (5-day)</span>
        </button>
        <button id="toggleHistoricalComparison" class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700">
            <span class="show-text">🔍 Show Hist. Comparison</span>
            <span class="hide-text hidden">🔍 Hide Hist. Comparison</span>
        </button>
    </div>

    {{-- Chart Containers --}}
    <div class="bg-white shadow rounded p-6 hidden" id="candlestickContainer">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-xl font-semibold">Historical Price (Candlestick)</h2>
                <p class="text-sm text-gray-500">Open, High, Low, Close price action.</p>
            </div>
            <button onclick="hideCandlestick()" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <canvas id="candlestickChart"></canvas> {{-- Canvas for the new chart --}}
    </div>

    <div class="bg-white shadow rounded p-6 hidden" id="predictionContainer">
        {{-- Existing Prediction Container --}}
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-xl font-semibold">Price Prediction</h2>
                <p class="text-sm text-gray-500">Historical data with predicted future prices.</p>
            </div>
            <button onclick="hidePrediction()" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <canvas id="stockChart"></canvas>
    </div>

    {{-- RSI Container --}}
    <div class="bg-white shadow rounded p-6 hidden" id="rsiContainer">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-xl font-semibold">Relative Strength Index (RSI)</h2>
                <p class="text-sm text-gray-500">RSI above 70 = overbought. Below 30 = oversold.</p>
            </div>
            <button onclick="hideRSI()" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <canvas id="rsiChart"></canvas>
    </div>

    {{-- MACD Container --}}
    <div class="bg-white shadow rounded p-6 hidden" id="macdContainer">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-xl font-semibold">MACD (Moving Average Convergence Divergence)</h2>
                <p class="text-sm text-gray-500">MACD crossing signal may suggest action.</p>
            </div>
            <button onclick="hideMACD()" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <canvas id="macdChart"></canvas>
    </div>

    {{-- OBV Container --}}
    <div class="bg-white shadow rounded p-6 hidden" id="obvContainer">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-xl font-semibold">On-Balance Volume (OBV)</h2>
                <p class="text-sm text-gray-500">Shows cumulative volume flow.</p>
            </div>
            <button onclick="hideOBV()" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <canvas id="obvChart"></canvas>
    </div>

    {{-- MA Container --}}
    <div class="bg-white shadow rounded p-6 hidden" id="maContainer">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-xl font-semibold">Moving Averages (MA)</h2>
                <p class="text-sm text-gray-500">Displays 10-day and 50-day simple moving averages.</p>
            </div>
            <button onclick="hideMA()" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <canvas id="maChart"></canvas>
    </div>

    {{-- Volatility Container --}}
    <div class="bg-white shadow rounded p-6 hidden" id="volatilityContainer">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-xl font-semibold">Historical Volatility</h2>
                <p class="text-sm text-gray-500">21-day standard deviation of daily percentage returns.</p>
            </div>
            <button onclick="hideVolatility()" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <canvas id="volatilityChart"></canvas>
    </div>

    {{-- ROC Container --}}
    <div class="bg-white shadow rounded p-6 hidden" id="rocContainer">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-xl font-semibold">Rate of Change (ROC)</h2>
                <p class="text-sm text-gray-500">5-day percentage price change.</p>
            </div>
            <button onclick="hideROC()" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <canvas id="rocChart"></canvas>
    </div>

    {{-- Historical Comparison Container --}}
    <div class="bg-white shadow rounded p-6 hidden" id="historicalComparisonContainer">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-xl font-semibold">Historical Performance Comparison</h2>
                <p class="text-sm text-gray-500">Actual close price vs. Model's prediction for that day.</p>
            </div>
            <button onclick="hideHistoricalComparison()" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <canvas id="historicalComparisonChart"></canvas>
    </div>

</div> 

{{-- JavaScript Section --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@^3"></script>
{{-- Luxon Date Adapter (Dependency for financial chart time scale) --}}
<script src="https://cdn.jsdelivr.net/npm/luxon@^2"></script> {{-- Use Luxon v2 or v3 --}}
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-luxon@^1"></script>
{{-- Financial Chart Plugin (Candlestick) --}}
<script src="https://cdn.jsdelivr.net/npm/chartjs-chart-financial@^0.1.0"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-annotation/1.4.0/chartjs-plugin-annotation.min.js" integrity="sha512-HrwQ3s6gKuOGtD7JsrHcFgzf6hDyANptKeR4pgyF/DREuDOQzC6hH5wQZu/d8cGw9Y+SxfJ0R+vF5QGAnj5+pA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>

let stockChart, rsiChart, macdChart, obvChart, maChart, volatilityChart, rocChart, historicalComparisonChart, candlestickChart; // Added candlestickChart

// --- Initialize Charts ---
function initStockChart() {
    const ctx = document.getElementById('stockChart')?.getContext('2d');
    if (!ctx) return; 
    stockChart = new Chart(ctx, {
        type: 'line',
        data: { datasets: [] }, 
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: { mode: 'index', intersect: false, callbacks: { label: ctx => `${ctx.dataset.label}: $${ctx.parsed.y?.toFixed(2)}` } }
            },
            scales: {
                y: { title: { display: true, text: 'Price ($)' }, beginAtZero: false },
                x: {
                    type: 'time', 
                    time: {
                        unit: 'day',
                        tooltipFormat: 'DD MMM yyyy', 
                        displayFormats: { day: 'DD MMM' } 
                    },
                    title: { display: true, text: 'Date' },
                    grid: { display: false }
                }
            }
        }
    });
}

// --- Toggle/Hide Functions ---
function toggleChartVisibility(containerId, buttonId, loadFunction) {
    const container = document.getElementById(containerId);
    const button = document.getElementById(buttonId);
    if (!container || !button) return;

    const showText = button.querySelector('.show-text');
    const hideText = button.querySelector('.hide-text');

    if (container.classList.contains('hidden')) {
        // Show
        if (loadFunction) loadFunction();
        container.classList.remove('hidden');
        if (showText) showText.classList.add('hidden');
        if (hideText) hideText.classList.remove('hidden');
    } else {
        // Hide
        container.classList.add('hidden');
        if (showText) showText.classList.remove('hidden');
        if (hideText) hideText.classList.add('hidden');
       
    }
}

// Specific toggle functions using the helper
function toggleCandlestick() { toggleChartVisibility('candlestickContainer', 'toggleCandlestick', loadCandlestickChart); }
function hideCandlestick() { toggleChartVisibility('candlestickContainer', 'toggleCandlestick'); } 

function togglePrediction() { toggleChartVisibility('predictionContainer', 'togglePrediction'); } 
function hidePrediction() { toggleChartVisibility('predictionContainer', 'togglePrediction'); }

function toggleRSI() { toggleChartVisibility('rsiContainer', 'toggleRSI', loadRSI); }
function hideRSI() { toggleChartVisibility('rsiContainer', 'toggleRSI'); }

function toggleMACD() { toggleChartVisibility('macdContainer', 'toggleMACD', loadMACD); }
function hideMACD() { toggleChartVisibility('macdContainer', 'toggleMACD'); }

function toggleOBV() { toggleChartVisibility('obvContainer', 'toggleOBV', loadOBV); }
function hideOBV() { toggleChartVisibility('obvContainer', 'toggleOBV'); }

function toggleMA() { toggleChartVisibility('maContainer', 'toggleMA', loadMA); }
function hideMA() { toggleChartVisibility('maContainer', 'toggleMA'); }

function toggleVolatility() { toggleChartVisibility('volatilityContainer', 'toggleVolatility', loadVolatility); }
function hideVolatility() { toggleChartVisibility('volatilityContainer', 'toggleVolatility'); }

function toggleROC() { toggleChartVisibility('rocContainer', 'toggleROC', loadROC); }
function hideROC() { toggleChartVisibility('rocContainer', 'toggleROC'); }

function toggleHistoricalComparison() { toggleChartVisibility('historicalComparisonContainer', 'toggleHistoricalComparison', loadHistoricalComparisonChart); }
function hideHistoricalComparison() { toggleChartVisibility('historicalComparisonContainer', 'toggleHistoricalComparison'); }


// --- Chart Update/Load Functions ---

// main stock chart
function updateChart(history, predictions) {
    const lastHistDateStr = history.length > 0 ? history[history.length - 1].date : null;
    let predictionPoints = []; 

    if (lastHistDateStr) {
        try {
            const lastDate = luxon.DateTime.fromISO(lastHistDateStr, { zone: 'utc' });
            if (!lastDate.isValid) throw new Error('Invalid history date format: ' + lastHistDateStr);

            predictionPoints = predictions.map((price, i) => {
                const nextDate = lastDate.plus({ days: i + 1 });
                return { x: nextDate.valueOf(), y: price }; 
            });
        } catch (e) {
            console.error("Error processing dates for prediction chart:", e);
            predictionPoints = [];
        }
    } else {
        console.warn("No history data to align predictions.");
    }


    const historyPoints = history.map(h => {
        try {
            const dt = luxon.DateTime.fromISO(h.date, { zone: 'utc' });
            return dt.isValid ? { x: dt.valueOf(), y: h.close } : null;
        } catch (e) {
            console.error(`Error parsing history date ${h.date}:`, e);
            return null;
        }
    }).filter(p => p !== null); 

    if (!stockChart) { initStockChart(); }
    if (!stockChart) return; 

    stockChart.data.datasets = [
        { label: 'History', data: historyPoints, borderColor: 'rgb(75, 192, 192)', backgroundColor: 'rgba(75, 192, 192, 0.1)', borderWidth: 2, tension: 0.1, fill: true, pointRadius: 1 },
        { label: 'Predicted', data: predictionPoints, borderColor: 'rgb(255, 99, 132)', backgroundColor: 'rgba(255, 99, 132, 0.1)', borderDash: [5, 5], borderWidth: 2, tension: 0.1, fill: false, pointRadius: 2, spanGaps: false }
    ];


    stockChart.update();

    const container = document.getElementById('predictionContainer');
    if (container?.classList.contains('hidden')) {
        toggleChartVisibility('predictionContainer', 'togglePrediction'); 
    }
}

// Load technical indicators data 
async function loadIndicators() {
    const ticker = document.getElementById('tickerInput')?.value;
    if (!ticker) {
        console.warn('Ticker needed for loadIndicators');
        return null; 
    }
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrfToken) throw new Error('Security token missing.');

        const res = await fetch(`/stock/indicators`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/x-www-form-urlencoded', 'Accept': 'application/json' },
            body: new URLSearchParams({ ticker: ticker, days: 100 })
        });

        if (!res.ok) {
            let errorDetail = `HTTP ${res.status}`;
            try { const errorData = await res.json(); errorDetail = errorData.error || errorData.detail || JSON.stringify(errorData); }
            catch (e) { try { errorDetail = await res.text(); } catch (e2) {} }
            throw new Error(`Failed to load indicators: ${errorDetail}`);
        }
        const data = await res.json();
        if (!data || typeof data !== 'object') throw new Error("Invalid data structure for indicators.");
        return data;
    } catch (err) {
        console.error('Failed to load indicators:', err);
        throw err; 
    }
}


function displayChartError(containerId, canvasId, error) {
    const container = document.getElementById(containerId);
    const canvas = document.getElementById(canvasId);
    if (canvas) canvas.style.display = 'none';
    const existingError = container?.querySelector('.chart-error-message');
    if (existingError) existingError.remove();
    const errorMsg = document.createElement('p');
    errorMsg.textContent = `Could not load chart: ${error.message || 'Unknown error'}`;
    errorMsg.className = 'text-red-600 p-4 chart-error-message';
    if (container) {
        container.appendChild(errorMsg);
        container.classList.remove('hidden');
    }
}

// Chart Loading Functions 

async function loadRSI() {
    const containerId = 'rsiContainer'; const canvasId = 'rsiChart';
    try {
        const data = await loadIndicators();
        if (!data?.rsi?.length) throw new Error("No RSI data.");
        const canvas = document.getElementById(canvasId); if (!canvas) throw new Error("Canvas not found.");
        canvas.style.display = 'block'; document.getElementById(containerId)?.querySelector('.chart-error-message')?.remove();

        const labels = data.rsi.map(item => item.date); 
        const values = data.rsi.map(item => item.RSI);

        const ctx = canvas.getContext('2d');
        const config = { /* ... RSI config using category axis ... */
             type: 'line', data: { labels: labels, datasets: [ { label: 'RSI (14)', data: values, borderColor: 'rgb(124, 58, 237)', /*...*/ pointRadius: 0 }, { label: 'Overbought (70)', data: Array(values.length).fill(70), borderColor: 'rgb(239, 68, 68)', /*...*/ pointRadius: 0 }, { label: 'Oversold (30)', data: Array(values.length).fill(30), borderColor: 'rgb(16, 185, 129)', /*...*/ pointRadius: 0 } ] }, options: { responsive: true, maintainAspectRatio: true, plugins: { /*...*/ }, scales: { y: { min: 0, max: 100, /*...*/ title: { display: true, text: 'RSI Value' }}, x: { type: 'category', title: { display: true, text: 'Date' }, grid: { display: false }, ticks: { autoSkip: true, maxRotation: 0 }}}, /*...*/ }
        };
        if (rsiChart) rsiChart.destroy();
        rsiChart = new Chart(ctx, config);
    } catch (error) { console.error("RSI Error:", error); displayChartError(containerId, canvasId, error); }
}

async function loadMACD() {
    const containerId = 'macdContainer'; const canvasId = 'macdChart';
    try {
        const data = await loadIndicators();
        if (!data?.macd?.length) throw new Error("No MACD data.");
        const canvas = document.getElementById(canvasId); if (!canvas) throw new Error("Canvas not found.");
        canvas.style.display = 'block'; document.getElementById(containerId)?.querySelector('.chart-error-message')?.remove();

        const labels = data.macd.map(item => item.date); 
        const macdValues = data.macd.map(item => item.MACD);
        const signalValues = data.macd.map(item => item.Signal);

        const ctx = canvas.getContext('2d');
        const config = { /* ... MACD config using category axis ... */
            type: 'line', data: { labels: labels, datasets: [ { label: 'MACD', data: macdValues, borderColor: 'rgb(59, 130, 246)', /*...*/ pointRadius: 0 }, { label: 'Signal Line', data: signalValues, borderColor: 'rgb(234, 88, 12)', /*...*/ pointRadius: 0 } ] }, options: { responsive: true, maintainAspectRatio: true, plugins: { /*...*/ }, scales: { x: { type: 'category', title: { display: true, text: 'Date' }, /*...*/ ticks: { autoSkip: true, maxRotation: 0 }}, y: { title: { display: true, text: 'MACD Value' }, /*...*/ }}, /*...*/ }
        };
        if (macdChart) macdChart.destroy();
        macdChart = new Chart(ctx, config);
    } catch (error) { console.error("MACD Error:", error); displayChartError(containerId, canvasId, error); }
}

async function loadOBV() {
    const containerId = 'obvContainer'; const canvasId = 'obvChart';
    try {
        const data = await loadIndicators();
        if (!data?.obv?.length) throw new Error("No OBV data.");
        const canvas = document.getElementById(canvasId); if (!canvas) throw new Error("Canvas not found.");
        canvas.style.display = 'block'; document.getElementById(containerId)?.querySelector('.chart-error-message')?.remove();

        const labels = data.obv.map(item => item.date); 
        const values = data.obv.map(item => item.OBV);

        const ctx = canvas.getContext('2d');
        const config = { /* ... OBV config using category axis ... */
            type: 'line', data: { labels: labels, datasets: [ { label: 'OBV', data: values, borderColor: 'rgb(6, 182, 212)', /*...*/ pointRadius: 0 } ] }, options: { responsive: true, maintainAspectRatio: true, plugins: { /*...*/ }, scales: { y: { title: { display: true, text: 'On-Balance Volume' }, ticks: { callback: function(value) { /*...*/ return value.toLocaleString(); }}}, x: { type: 'category', title: { display: true, text: 'Date' }, /*...*/ ticks: { autoSkip: true, maxRotation: 0 }}}, /*...*/ }
        };
        if (obvChart) obvChart.destroy();
        obvChart = new Chart(ctx, config);
    } catch (error) { console.error("OBV Error:", error); displayChartError(containerId, canvasId, error); }
}

async function loadMA() {
    const containerId = 'maContainer'; const canvasId = 'maChart';
    try {
        const data = await loadIndicators();
        if (!data?.ma?.length) throw new Error("No MA data.");
        const canvas = document.getElementById(canvasId); if (!canvas) throw new Error("Canvas not found.");
        canvas.style.display = 'block'; document.getElementById(containerId)?.querySelector('.chart-error-message')?.remove();

        const labels = data.ma.map(item => item.date); 
        const ma10Values = data.ma.map(item => item.MA10);
        const ma50Values = data.ma.map(item => item.MA50);

        const ctx = canvas.getContext('2d');
        const config = { /* ... MA config using category axis ... */
            type: 'line', data: { labels: labels, datasets: [ { label: 'MA 10', data: ma10Values, borderColor: 'rgb(59, 130, 246)', /*...*/ pointRadius: 0 }, { label: 'MA 50', data: ma50Values, borderColor: 'rgb(234, 88, 12)', /*...*/ pointRadius: 0 } ] }, options: { responsive: true, maintainAspectRatio: true, plugins: { /*...*/ }, scales: { y: { title: { display: true, text: 'Price ($)' }, ticks: { callback: function(value) { return '$' + value.toFixed(2); }}}, x: { type: 'category', title: { display: true, text: 'Date' }, /*...*/ ticks: { autoSkip: true, maxRotation: 0 }}}, /*...*/ }
        };
        if (maChart) maChart.destroy();
        maChart = new Chart(ctx, config);
    } catch (error) { console.error("MA Error:", error); displayChartError(containerId, canvasId, error); }
}

async function loadVolatility() {
    const containerId = 'volatilityContainer'; const canvasId = 'volatilityChart';
    try {
        const data = await loadIndicators();
        if (!data?.volatility?.length) throw new Error("No Volatility data.");
        const canvas = document.getElementById(canvasId); if (!canvas) throw new Error("Canvas not found.");
        canvas.style.display = 'block'; document.getElementById(containerId)?.querySelector('.chart-error-message')?.remove();

        const labels = data.volatility.map(item => item.date); 
        const values = data.volatility.map(item => (item.Volatility !== null && !isNaN(item.Volatility)) ? item.Volatility * 100 : null);

        const ctx = canvas.getContext('2d');
        const config = { /* ... Volatility config using category axis ... */
            type: 'line', data: { labels: labels, datasets: [ { label: 'Volatility (21-day)', data: values, borderColor: 'rgb(220, 38, 38)', /*...*/ pointRadius: 0 } ] }, options: { responsive: true, maintainAspectRatio: true, plugins: { /*...*/ }, scales: { y: { title: { display: true, text: 'Volatility (%)' }, ticks: { callback: function(value) { return value.toFixed(1) + '%'; }}, beginAtZero: true }, x: { type: 'category', title: { display: true, text: 'Date' }, /*...*/ ticks: { autoSkip: true, maxRotation: 0 }}}, /*...*/ }
        };
        if (volatilityChart) volatilityChart.destroy();
        volatilityChart = new Chart(ctx, config);
    } catch (error) { console.error("Volatility Error:", error); displayChartError(containerId, canvasId, error); }
}

async function loadROC() {
    const containerId = 'rocContainer'; const canvasId = 'rocChart';
    try {
        const data = await loadIndicators();
        if (!data?.roc?.length) throw new Error("No ROC data.");
        const canvas = document.getElementById(canvasId); if (!canvas) throw new Error("Canvas not found.");
        canvas.style.display = 'block'; document.getElementById(containerId)?.querySelector('.chart-error-message')?.remove();

        const labels = data.roc.map(item => item.date); 
        const values = data.roc.map(item => item.ROC_5);

        const ctx = canvas.getContext('2d');
        const config = { /* ... ROC config using category axis ... */
            type: 'line', data: { labels: labels, datasets: [ { label: 'ROC (5-day)', data: values, borderColor: 'rgb(236, 72, 153)', /*...*/ pointRadius: 0 } ] }, options: { responsive: true, maintainAspectRatio: true, plugins: { /*...*/ }, scales: { y: { title: { display: true, text: 'Rate of Change (%)' }, ticks: { callback: function(value) { return value.toFixed(1) + '%'; }}}, x: { type: 'category', title: { display: true, text: 'Date' }, /*...*/ ticks: { autoSkip: true, maxRotation: 0 }}}, /*...*/ }
        };
        if (rocChart) rocChart.destroy();
        rocChart = new Chart(ctx, config);
    } catch (error) { console.error("ROC Error:", error); displayChartError(containerId, canvasId, error); }
}

// Load Historical Comparison Chart 
async function loadHistoricalComparisonChart() {
    const containerId = 'historicalComparisonContainer'; const canvasId = 'historicalComparisonChart';
    const ticker = document.getElementById('tickerInput')?.value;
    if (!ticker) { console.warn('Ticker needed for historical comparison'); hideHistoricalComparison(); return; }

    const canvas = document.getElementById(canvasId); if (!canvas) { console.error("Canvas not found."); return; }
    canvas.style.display = 'block'; document.getElementById(containerId)?.querySelector('.chart-error-message')?.remove();

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrfToken) throw new Error("CSRF token missing.");

        const res = await fetch(`/stock/predict-historical`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/x-www-form-urlencoded', 'Accept': 'application/json' },
            body: new URLSearchParams({ ticker: ticker, days: 60 })
        });
        if (!res.ok) { /* ... error handling ... */ throw new Error(/* ... */); }
        const data = await res.json();
        if (!data?.comparison?.length) throw new Error("No comparison data.");
        document.getElementById(containerId)?.classList.remove('hidden');

    
        const actualData = data.comparison.map(item => ({ x: luxon.DateTime.fromISO(item.date, { zone: 'utc' }).valueOf(), y: item.actual_close }));
        const predictedData = data.comparison.map(item => ({ x: luxon.DateTime.fromISO(item.date, { zone: 'utc' }).valueOf(), y: item.predicted_close }));

        const ctx = canvas.getContext('2d');
        const config = {
            type: 'line',
            data: {
                datasets: [
                    { label: 'Actual Close', data: actualData, borderColor: 'rgb(75, 192, 192)', /*...*/ pointRadius: 1, fill: false },
                    { label: 'Model Predicted (Historical)', data: predictedData, borderColor: 'rgb(255, 159, 64)', /*...*/ pointRadius: 1, fill: false, spanGaps: true }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: true, plugins: { /*...*/ },
                scales: {
                    y: { title: { display: true, text: 'Price ($)' }, ticks: { callback: function(value) { return '$' + value.toFixed(2); }}},
                    x: { type: 'time', time: { unit: 'day', tooltipFormat: 'DD MMM yyyy', displayFormats: { day: 'DD MMM' } }, title: { display: true, text: 'Date' }, grid: { display: false }, ticks: { autoSkip: true, maxRotation: 0 }}
                }, /*...*/
            }
        };
        if (historicalComparisonChart) historicalComparisonChart.destroy();
        historicalComparisonChart = new Chart(ctx, config);
    } catch (error) { console.error("Hist Comparison Error:", error); displayChartError(containerId, canvasId, error); }
}


//CANDLESTICK CHART LOAD FUNCTION 
async function loadCandlestickChart() {
    const containerId = 'candlestickContainer';
    const canvasId = 'candlestickChart';
    const container = document.getElementById(containerId);
    const canvas = document.getElementById(canvasId);
    const ticker = document.getElementById('tickerInput')?.value; 

    if (!ticker) {
        console.warn('Ticker needed for candlestick chart');
        hideCandlestick(); 
        return;
    }

 
    if (!canvas) { console.error(`Canvas element #${canvasId} not found.`); return; }
    canvas.style.display = 'block';
    container?.querySelector('.chart-error-message')?.remove(); 

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrfToken) throw new Error("CSRF token missing.");

        const res = await fetch(`/stock/ohlc-history`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/x-www-form-urlencoded',
                'Accept': 'application/json'
            },
            body: new URLSearchParams({ ticker: ticker, days: 100 }) 
        });

        if (!res.ok) {
            let errorDetail = `HTTP error ${res.status}`;
            try {
                const errorData = await res.json();
                errorDetail = errorData.error || errorData.detail || JSON.stringify(errorData);
            } catch (e) { try { errorDetail = await res.text(); } catch (e2) {} }
            throw new Error(`Failed to load OHLC data: ${errorDetail}`);
        }

        const data = await res.json();

        
        if (!data?.ohlc_data || !Array.isArray(data.ohlc_data) || data.ohlc_data.length === 0) {
            throw new Error("No OHLC data returned from server or data is empty.");
        }

        container?.classList.remove('hidden');

        const candlestickData = data.ohlc_data.map(item => {
             try {
                const dt = luxon.DateTime.fromISO(item.date, { zone: 'utc' }); 
                if (!dt.isValid) { console.warn(`Invalid date format skipped: ${item.date}`); return null; }
                return { x: dt.valueOf(), o: item.o, h: item.h, l: item.l, c: item.c };
             } catch(e) {
                 console.error(`Error processing OHLC data for date ${item.date}:`, e);
                 return null;
             }
        }).filter(item => item !== null); 

        if (candlestickData.length === 0) {
             throw new Error("Failed to parse dates or format candlestick data after filtering.");
        }

        const ctx = canvas.getContext('2d');
        const config = {
            type: 'candlestick', 
            data: {
                datasets: [{
                    label: `${ticker} OHLC`,
                    data: candlestickData,
                    color: {
                         up: 'rgba(80, 160, 115, 1)',      // Green
                         down: 'rgba(215, 85, 65, 1)',     // Red
                         unchanged: 'rgba(90, 90, 90, 1)', // Grey
                    }
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    x: {
                        type: 'time', 
                        time: {
                            unit: 'day',
                            tooltipFormat: 'DD MMM yyyy', 
                            displayFormats: { day: 'DD MMM' } 
                        },
                        title: { display: true, text: 'Date' },
                        grid: { display: false },
                        ticks: { source: 'auto', maxRotation: 0, autoSkip: true }
                    },
                    y: {
                        title: { display: true, text: 'Price ($)' },
                        ticks: { callback: function(value) { return '$' + value.toFixed(2); } }
                    }
                },
                plugins: {
                    legend: { display: true, position: 'top' },
                    tooltip: { mode: 'index', intersect: false } 
                }
            }
        };

        if (candlestickChart) { candlestickChart.destroy(); }
        candlestickChart = new Chart(ctx, config);

    } catch (error) {
        console.error("Candlestick Chart Error:", error);
        displayChartError(containerId, canvasId, error); 
    }
}


// Initialize charts on page load
document.addEventListener('DOMContentLoaded', function() {
    initStockChart(); // Initialize prediction chart shell

    // Set up toggle buttons using the helper function
    document.getElementById('toggleCandlestick')?.addEventListener('click', toggleCandlestick);
    document.getElementById('togglePrediction')?.addEventListener('click', togglePrediction);
    document.getElementById('toggleRSI')?.addEventListener('click', toggleRSI);
    document.getElementById('toggleMACD')?.addEventListener('click', toggleMACD);
    document.getElementById('toggleOBV')?.addEventListener('click', toggleOBV);
    document.getElementById('toggleMA')?.addEventListener('click', toggleMA);
    document.getElementById('toggleVolatility')?.addEventListener('click', toggleVolatility);
    document.getElementById('toggleROC')?.addEventListener('click', toggleROC);
    document.getElementById('toggleHistoricalComparison')?.addEventListener('click', toggleHistoricalComparison);

    if (!document.querySelector('meta[name="csrf-token"]')) {
        console.warn("CSRF token meta tag not found.");
    }
});

// Handle prediction form submission
document.getElementById('predictionForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const ticker = formData.get('ticker');
    const days = formData.get('days');
    const predictionBox = document.getElementById('predictionValue');
    const valueSpan = document.getElementById('predictedValue');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    
    if (!ticker) { alert("Please enter a ticker symbol."); return; }
    if (!csrfToken) { console.error("CSRF token missing."); valueSpan.textContent = 'Error: Security token missing.'; /*...*/ return; }
    if (!predictionBox || !valueSpan) return; // Exit if elements not found

    
    valueSpan.textContent = 'Loading...';
    predictionBox.className = 'mt-4 px-4 py-3 rounded bg-gray-100 text-gray-800 border border-gray-300';
    predictionBox.classList.remove('hidden');

    try {
        const predictionRes = await fetch('/api/stock/predict?days=' + days, {
            method: 'POST', body: formData, headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        });
        const predictionData = await predictionRes.json();
        if (!predictionRes.ok || !predictionData.predicted_prices) {
            const errorDetail = predictionData.detail || predictionData.error || 'Prediction request failed.';
            throw new Error(errorDetail);
        }

        
        const predictedPrices = predictionData.predicted_prices;
        valueSpan.textContent = predictedPrices.map(p => `$${p.toFixed(2)}`).join(', ');
        predictionBox.className = 'mt-4 px-4 py-3 rounded bg-green-100 text-green-800 border border-green-300';

        let updatedHistory = [];
        try {
            const historyRes = await fetch('/stock/history', {
                method: 'POST', body: new URLSearchParams({ ticker: ticker, days: 60 }), headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' }
            });
            const historyData = await historyRes.json();
            if (historyRes.ok && historyData.history?.length) {
                updatedHistory = historyData.history.map(item => ({ date: item.date, close: parseFloat(item.close) }));
                updateChart(updatedHistory, predictedPrices); 
            } else { console.warn("History fetch failed/empty for prediction chart."); if (stockChart) { stockChart.data.datasets = []; stockChart.update(); } }
        } catch (historyError) { console.error("Error processing history/prediction chart:", historyError); if (stockChart) { stockChart.data.datasets = []; stockChart.update(); } }

        // Trigger loading/reloading of any visible charts
        const chartsToLoadPromises = [];
        if (!document.getElementById('candlestickContainer')?.classList.contains('hidden')) { chartsToLoadPromises.push(loadCandlestickChart()); }
        if (!document.getElementById('rsiContainer')?.classList.contains('hidden')) { chartsToLoadPromises.push(loadRSI()); }
        if (!document.getElementById('macdContainer')?.classList.contains('hidden')) { chartsToLoadPromises.push(loadMACD()); }
        if (!document.getElementById('obvContainer')?.classList.contains('hidden')) { chartsToLoadPromises.push(loadOBV()); }
        if (!document.getElementById('maContainer')?.classList.contains('hidden')) { chartsToLoadPromises.push(loadMA()); }
        if (!document.getElementById('volatilityContainer')?.classList.contains('hidden')) { chartsToLoadPromises.push(loadVolatility()); }
        if (!document.getElementById('rocContainer')?.classList.contains('hidden')) { chartsToLoadPromises.push(loadROC()); }
        if (!document.getElementById('historicalComparisonContainer')?.classList.contains('hidden')) { chartsToLoadPromises.push(loadHistoricalComparisonChart()); }

        await Promise.allSettled(chartsToLoadPromises); 
        console.log("Visible charts update attempted.");

    } catch (err) { // Catch errors primarily from the prediction fetch
        console.error("Prediction form submission error:", err);
        valueSpan.textContent = `Error: ${err.message || 'An unexpected error occurred.'}`;
        predictionBox.className = 'mt-4 px-4 py-3 rounded bg-red-100 text-red-800 border border-red-300';
    }
});
</script>
@endsection
