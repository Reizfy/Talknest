<x-app-layout :assets="$assets ?? []">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Edit Post</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('superadmin.posts.update', $post->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group mb-3">
                                <label for="name">Title</label>
                                <input type="text" class="form-control" id="title" name="title" value="{{ $post->title }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="nest">Nest</label>
                                <input type="text" class="form-control" id="nest" name="nest" value="{{ $post->nest->name }}" disabled>
                            </div>
                            <div class="form-group mb-3">
                                <label for="author">Author</label>
                                <input type="text" class="form-control" id="author" name="author" value="{{ $post->user->username }}" disabled>
                            </div>
                            <button type="submit" class="btn btn-warning">Update Nest</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.components.share-offcanvas')
</x-app-layout>
