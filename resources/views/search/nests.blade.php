<x-app-layout :assets="$assets ?? []">
    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h3 class="mb-4">All Nests</h3>
                <ul class="list-unstyled mb-3">
                    @forelse ($nests as $nest)
                        <li class="mb-2">
                            <a href="{{ route('nests.index', ['name' => $nest->name]) }}" class="text-decoration-none">
                                {{ $nest->name }}
                            </a>
                        </li>
                    @empty
                        <li class="text-muted">No nests found.</li>
                    @endforelse
                </ul>
                {{ $nests->links() }}
            </div>
        </div>
    </div>
    @include('partials.components.share-offcanvas')
</x-app-layout>
