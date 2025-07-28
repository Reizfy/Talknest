<x-app-layout :assets="$assets ?? []">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <div class="d-flex flex-wrap align-items-center">
                            <div class="profile-img position-relative me-3 mb-3 mb-lg-0">
                                <img src="{{ $user->avatar ? asset('storage/profiles/images/' . $user->avatar) : asset('images/avatars/default.jpg') }}"
                                    alt="User-Profile" class="img-fluid rounded-pill avatar-100">
                            </div>
                            <div class="d-flex flex-wrap align-items-center mb-3 mb-sm-0">
                                <h4 class="me-2 h4">{{ $user->full_name ?? ($user->username ?? 'User') }}</h4>
                            </div>
                        </div>
                        @if (auth()->check() && auth()->id() === $user->id)
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary">Edit Profile</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row d-flex justify-content-center">
            <div class="col-lg-6">
                  <div class="profile-content tab-content">
                     <div id="profile-feed">
                           <!-- User's posts will be loaded here dynamically -->
                     </div>
                  </div>
            </div>
        </div>
    </div>

    @include('partials.components.share-offcanvas')
   <script>
      window.profileUserId = {{ $user->id }};
   </script>
   <script src="/js/profile-feed.js"></script>
   <script src="/js/profile-detail.js"></script>
</x-app-layout>

