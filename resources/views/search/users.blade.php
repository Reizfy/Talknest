<x-app-layout :assets="$assets ?? []">
    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h3 class="mb-4">All Users</h3>
                <ul class="list-unstyled mb-3">
                    @forelse ($users as $user)
                        <li class="mb-2">
                            <a href="{{ route('users.index', ['user' => $user->username]) }}" class="text-decoration-none">
                                {{ $user->username }}
                            </a>
                        </li>
                    @empty
                        <li class="text-muted">No users found.</li>
                    @endforelse
                </ul>
                {{ $users->links() }}
            </div>
        </div>
    </div>
    @include('partials.components.share-offcanvas')
</x-app-layout>
