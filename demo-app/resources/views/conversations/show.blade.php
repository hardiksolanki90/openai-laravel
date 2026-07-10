<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $conversation->title }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                @foreach ($conversation->messages as $message)
                    <div class="{{ $message['role'] === 'user' ? 'text-right' : 'text-left' }}">
                        <span class="inline-block px-4 py-2 rounded-lg {{ $message['role'] === 'user' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-800' }}">
                            {{ $message['content'] }}
                        </span>
                    </div>
                @endforeach
            </div>

            <form method="POST" action="{{ route('conversations.messages.store', $conversation) }}" class="flex gap-2">
                @csrf
                <input type="text" name="content" placeholder="Type a message..." required
                       class="flex-1 rounded-md border-gray-300 shadow-sm">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Send</button>
            </form>

            <p class="text-sm text-gray-500">
                {{ $conversation->total_tokens }} tokens &middot; ${{ number_format($conversation->total_cost, 4) }}
            </p>
        </div>
    </div>
</x-app-layout>
