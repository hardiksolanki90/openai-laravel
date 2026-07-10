<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Budget</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('settings.budget.update') }}" class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                @csrf
                @method('PUT')

                <label class="block">
                    <span class="text-sm text-gray-600">Monthly limit ($)</span>
                    <input type="number" step="0.01" name="monthly_limit" value="{{ $budget->monthly_limit }}" required class="w-full rounded-md border-gray-300 shadow-sm">
                </label>

                <label class="block">
                    <span class="text-sm text-gray-600">Warning threshold ($)</span>
                    <input type="number" step="0.01" name="warning_threshold" value="{{ $budget->warning_threshold }}" required class="w-full rounded-md border-gray-300 shadow-sm">
                </label>

                <label class="flex items-center gap-2">
                    <input type="checkbox" name="block_on_limit" value="1" @checked($budget->block_on_limit)>
                    <span class="text-sm text-gray-600">Block requests once the limit is reached</span>
                </label>

                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Save</button>
            </form>
        </div>
    </div>
</x-app-layout>
