<x-app-layout :assets="$assets ?? []">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Edit User</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('superadmin.users.update', $user->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-group mb-3">
                                <label for="name">Username</label>
                                <input type="text" class="form-control" id="username" name="username" value="{{ $user->username }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="description">Email</label>
                                <input type="text" class="form-control" id="email" name="email" value="{{ $user->email }}" required></input>
                            </div>
                            <div class="form-group mb-3">
                                <label for="description">Role</label>
                                <select class="form-select" id="user_type" name="user_type" required>
                                    <option value="admin" {{ $user->user_type == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="user" {{ $user->user_type == 'user' ? 'selected' : '' }}>User</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="status">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
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
