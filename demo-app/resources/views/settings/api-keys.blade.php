<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">API Keys</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <form method="POST" action="{{ route('settings.api-keys.store') }}" class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                @csrf
                <input type="text" name="name" placeholder="Key name" required class="w-full rounded-md border-gray-300 shadow-sm">
                <input type="password" name="openai_key" placeholder="sk-..." required class="w-full rounded-md border-gray-300 shadow-sm">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Add key</button>
            </form>

            <div class="bg-white shadow-sm sm:rounded-lg divide-y">
                @foreach ($keys as $key)
                    <div class="p-4 flex justify-between items-center">
                        <div>
                            <p class="font-medium">{{ $key->name }}</p>
                            <p class="text-sm text-gray-500">{{ $key->key_masked }} &middot; {{ $key->is_active ? 'active' : 'revoked' }}</p>
                        </div>
                        @if ($key->is_active)
                            <form method="POST" action="{{ route('settings.api-keys.destroy', $key) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600">Revoke</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
