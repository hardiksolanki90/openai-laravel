<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Team Members</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <form method="POST" action="{{ route('settings.team.invite') }}" class="bg-white shadow-sm sm:rounded-lg p-6 flex gap-2">
                @csrf
                <input type="number" name="user_id" placeholder="User ID" required class="rounded-md border-gray-300 shadow-sm">
                <select name="role" required class="rounded-md border-gray-300 shadow-sm">
                    <option value="admin">Admin</option>
                    <option value="member" selected>Member</option>
                    <option value="viewer">Viewer</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Invite</button>
            </form>

            <div class="bg-white shadow-sm sm:rounded-lg divide-y">
                @foreach ($members as $member)
                    <div class="p-4 flex justify-between">
                        <span>{{ $member->user->name ?? 'User #'.$member->user_id }}</span>
                        <span class="text-sm text-gray-500">{{ $member->role }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
