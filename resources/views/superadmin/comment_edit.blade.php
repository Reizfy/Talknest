<x-app-layout :assets="$assets ?? []">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Edit Comment</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('superadmin.comments.update', $comment->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group mb-3">
                                <label for="name">Post</label>
                                <input type="text" class="form-control" id="post" name="post" value="{{ $comment->post->title }}" disabled>
                            </div>
                            <div class="form-group mb-3">
                                <label for="nest">Author</label>
                                <input type="text" class="form-control" id="author" name="author" value="{{ $comment->user->username }}" disabled>
                            </div>
                            <div class="form-group mb-3">
                                <label for="author">Content</label>
                                <input type="text" class="form-control" id="content" name="content" value="{{ $comment->content }}" required>
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
