<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Conversations</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <form method="POST" action="{{ route('conversations.store') }}" class="flex gap-2">
                @csrf
                <input type="text" name="title" placeholder="New conversation title" required
                       class="flex-1 rounded-md border-gray-300 shadow-sm">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Create</button>
            </form>

            <div class="bg-white shadow-sm sm:rounded-lg divide-y">
                @forelse ($conversations as $conversation)
                    <a href="{{ route('conversations.show', $conversation) }}" class="block p-4 hover:bg-gray-50">
                        <p class="font-medium">{{ $conversation->title }}</p>
                        <p class="text-sm text-gray-500">{{ $conversation->model }} &middot; {{ $conversation->total_tokens }} tokens &middot; ${{ number_format($conversation->total_cost, 4) }}</p>
                    </a>
                @empty
                    <p class="p-4 text-gray-500">No conversations yet.</p>
                @endforelse
            </div>

            {{ $conversations->links() }}
        </div>
    </div>
</x-app-layout>
