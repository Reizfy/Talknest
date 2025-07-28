<x-app-layout :assets="$assets ?? []">
    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h3 class="mb-4">All Posts</h3>
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
                {{ $posts->links() }}
            </div>
        </div>
    </div>
    @include('partials.components.share-offcanvas')

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const hash = window.location.hash;
        if (hash && hash.startsWith('#post-')) {
            const postId = hash.replace('#post-', '');
            // Option 1: If you have a modal trigger function
            if (typeof openPostDetailModal === 'function') {
                openPostDetailModal(postId);
            } else {
                // Option 2: Simulate click on .post-detail-link
                const postLink = document.querySelector('.post-detail-link[data-post="' + postId + '"]');
                if (postLink) {
                    postLink.click();
                }
            }
        }
    });
    </script>
    @endpush
</x-app-layout>

