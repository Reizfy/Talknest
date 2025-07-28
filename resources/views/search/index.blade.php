<x-app-layout :assets="$assets ?? []">
    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h3 class="mb-4">Search Results</h3>
                <div class="row g-4">
                    <div class="col-md-4">
                        <h5>Posts</h5>
                        <ul class="list-unstyled mb-3">
                            @forelse ($posts as $post)
                                <li class="mb-2">
                                    <a href="{{ url('nests/' . ($post->nest->name ?? 'unknown') . '#post-' . $post->id) }}" class="text-decoration-none post-detail-link" data-nest="{{ $post->nest->name ?? 'unknown' }}" data-post="{{ $post->id }}">
                                        {{ $post->title }}
                                    </a>
                                    <span class="text-muted small">in {{ $post->nest->name ?? 'unknown' }}</span>
                                </li>
                            @empty
                                <li class="text-muted">No posts found.</li>
                            @endforelse
                        </ul>
                        <a href="{{ route('posts.search', ['q' => request('q')]) }}" class="btn btn-link p-0">See all posts</a>
                    </div>
                    <div class="col-md-4">
                        <h5>Nests</h5>
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
                        <a href="{{ route('nests.search', ['q' => request('q')]) }}" class="btn btn-link p-0">See all nests</a>
                    </div>
                    <div class="col-md-4">
                        <h5>Users</h5>
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
                        <a href="{{ route('users.search', ['q' => request('q')]) }}" class="btn btn-link p-0">See all users</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.components.share-offcanvas')
</x-app-layout>
