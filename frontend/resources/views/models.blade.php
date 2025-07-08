@extends('layouts.app')

@section('title', 'Trained Models')

@section('content')
<div class="bg-white shadow rounded p-8 max-w-5xl mx-auto">
    <h1 class="text-3xl font-semibold text-gray-800 mb-6">📦 Trained Stock Models</h1>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded bg-green-100 text-green-800 border border-green-300">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 px-4 py-3 rounded bg-red-100 text-red-800 border border-red-300">
            ❌ {{ session('error') }}
        </div>
    @endif

    @if(count($models))
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto border border-gray-200 rounded shadow-sm">
                <thead class="bg-gray-50 text-gray-700 text-sm uppercase tracking-wide border-b">
                    <tr>
                        <th class="px-6 py-3 text-left">Ticker</th>
                        <th class="px-6 py-3 text-left">Trained At</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm text-gray-800">
                    @foreach($models as $model)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-3 font-semibold">{{ $model['ticker'] }}</td>
                            <td class="px-6 py-3">{{ $model['trained_at'] }}</td>
                            <td class="px-6 py-3 text-right">
                                <form method="POST" action="/models/delete" onsubmit="return confirm('Delete {{ $model['ticker'] }} model?');">
                                    @csrf
                                    <input type="hidden" name="ticker" value="{{ $model['ticker'] }}">
                                    <button class="bg-red-500 hover:bg-red-600 text-white text-xs font-medium px-4 py-1.5 rounded">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-gray-500 mt-4">No trained models found. You can create one from the prediction page.</p>
    @endif
</div>
@endsection
