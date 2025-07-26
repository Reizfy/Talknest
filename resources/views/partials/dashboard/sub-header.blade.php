<div class="iq-navbar-header" style="height: 215px;">
    <div class="iq-header-img">
        @php
            $bannerSrc = null;
            if (isset($nest) && $nest) {
                $bannerSrc = $nest->banner ? asset('storage/nests/banners/' . $nest->banner) : asset('images/dashboard/top-header.png');
            } elseif (isset($user) && $user) {
                $bannerSrc = $user->banner ? asset('storage/users/banners/' . $user->banner) : asset('images/dashboard/top-header.png');
            } else {
                $bannerSrc = asset('images/dashboard/top-header.png');
            }
        @endphp
        <img src="{{ $bannerSrc }}" alt="header" class="theme-color-default-img img-fluid w-100 h-100">
    </div>
</div>