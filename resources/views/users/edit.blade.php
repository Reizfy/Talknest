<x-app-layout :assets="$assets ?? []">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Edit Profile</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" value="{{ old('username', $user->username) }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="full_name">Display Name</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" value="{{ old('full_name', $user->full_name) }}">
                            </div>
                            <div class="form-group mb-3">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="gender">Gender</label>
                                <select class="form-control" id="gender" name="gender">
                                    <option value="" {{ $user->gender == '' ? 'selected' : '' }}>Prefer not to say</option>
                                    <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ $user->gender == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="avatar">Avatar</label>
                                <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/profiles/images/' . $user->avatar) }}" alt="Current Avatar" class="mt-2 rounded-pill" style="width:60px;height:60px;">
                                @endif
                            </div>
                            <div class="form-group mb-3">
                                <label for="banner">Banner</label>
                                <input type="file" class="form-control" id="banner" name="banner" accept="image/*">
                                @if($user->banner)
                                    <img src="{{ asset('storage/profiles/banners/' . $user->banner) }}" alt="Current Banner" class="mt-2 rounded" style="width:100%;max-height:120px;object-fit:cover;">
                                @endif
                            </div>
                            <div class="form-group mb-3">
                                <label for="current_password">Current Password</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" autocomplete="current-password">
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="new_password">New Password</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" autocomplete="new-password">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="new_password_confirmation">Confirm New Password</label>
                                        <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" autocomplete="new-password">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-warning">Update Profile</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.components.share-offcanvas')
</x-app-layout>
