<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-500">Requests this month</p>
                <p class="text-3xl font-bold">{{ $stats->totalRequests }}</p>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-500">Tokens used</p>
                <p class="text-3xl font-bold">{{ number_format($stats->totalTokens) }}</p>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-500">Spend / Budget</p>
                <p class="text-3xl font-bold">${{ number_format($budget->current_spend, 2) }} / ${{ number_format($budget->monthly_limit, 2) }}</p>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ min(100, $budget->percentageUsed()) }}%"></div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
            <a href="{{ route('conversations.index') }}" class="text-indigo-600 hover:underline">View conversations &rarr;</a>
        </div>
    </div>
</x-app-layout>
