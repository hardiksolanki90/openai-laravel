<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Usage Reports</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 grid grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Requests</p>
                    <p class="text-2xl font-bold">{{ $stats->totalRequests }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tokens</p>
                    <p class="text-2xl font-bold">{{ number_format($stats->totalTokens) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Cost</p>
                    <p class="text-2xl font-bold">${{ number_format($stats->totalCost, 4) }}</p>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg divide-y">
                <div class="p-4 font-medium">By model</div>
                @foreach ($byModel as $model => $row)
                    <div class="p-4 flex justify-between">
                        <span>{{ $model }}</span>
                        <span class="text-sm text-gray-500">{{ $row['requests'] }} req &middot; {{ $row['tokens'] }} tok &middot; ${{ number_format($row['cost'], 4) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
