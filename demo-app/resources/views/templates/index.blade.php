<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Prompt Templates</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <form method="POST" action="{{ route('templates.store') }}" class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                @csrf
                <input type="text" name="name" placeholder="Template name" required class="w-full rounded-md border-gray-300 shadow-sm">
                <textarea name="content" rows="4" placeholder="Template content, use {{ variable }} placeholders" required class="w-full rounded-md border-gray-300 shadow-sm"></textarea>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Save template</button>
            </form>

            <div class="bg-white shadow-sm sm:rounded-lg divide-y">
                @forelse ($templates as $template)
                    <div class="p-4">
                        <p class="font-medium">{{ $template->name }}</p>
                        <p class="text-sm text-gray-500">{{ $template->slug }}</p>
                    </div>
                @empty
                    <p class="p-4 text-gray-500">No templates yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
