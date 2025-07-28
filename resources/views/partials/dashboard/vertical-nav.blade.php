<ul class="navbar-nav iq-main-menu" id="sidebar">
    <li class="nav-item">
        <a class="nav-link {{ activeRoute(route('home')) }}" aria-current="page" href="{{ route('home') }}">
            <i class="icon">
                <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.4"
                        d="M16.0756 2H19.4616C20.8639 2 22.0001 3.14585 22.0001 4.55996V7.97452C22.0001 9.38864 20.8639 10.5345 19.4616 10.5345H16.0756C14.6734 10.5345 13.5371 9.38864 13.5371 7.97452V4.55996C13.5371 3.14585 14.6734 2 16.0756 2Z"
                        fill="currentColor"></path>
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M4.53852 2H7.92449C9.32676 2 10.463 3.14585 10.463 4.55996V7.97452C10.463 9.38864 9.32676 10.5345 7.92449 10.5345H4.53852C3.13626 10.5345 2 9.38864 2 7.97452V4.55996C2 3.14585 3.13626 2 4.53852 2ZM4.53852 13.4655H7.92449C9.32676 13.4655 10.463 14.6114 10.463 16.0255V19.44C10.463 20.8532 9.32676 22 7.92449 22H4.53852C3.13626 22 2 20.8532 2 19.44V16.0255C2 14.6114 3.13626 13.4655 4.53852 13.4655ZM19.4615 13.4655H16.0755C14.6732 13.4655 13.537 14.6114 13.537 16.0255V19.44C13.537 20.8532 14.6732 22 16.0755 22H19.4615C20.8637 22 22 20.8532 22 19.44V16.0255C22 14.6114 20.8637 13.4655 19.4615 13.4655Z"
                        fill="currentColor"></path>
                </svg>
            </i>
            <span class="item-name">Home</span>
        </a>
    </li>
    @if ($canCreate)
    <li>
        <hr class="hr-horizontal">
    </li>

    <li class="nav-item static-item">
        <a class="nav-link static-item disabled" href="#" tabindex="-1">
            <span class="default-icon">Nests</span>
            <span class="mini-icon">-</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" aria-current="page" href="{{ route('nests.create') }}">
            <span class="icon d-flex align-items-center justify-content-center pt-1 m-0" style="width: 20px; min-width: 20px; max-width: 20px; height: 20px;">
                <svg class="icon-32" width="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.8672 7.21433L15.0865 14.0624C15.1805 14.2158 15.1715 14.4122 15.0605 14.5558C14.5594 15.2064 14.0514 15.7877 13.6244 16.1755C13.6244 16.1755 13.2824 16.5096 13.0654 16.6542C12.7693 16.8877 12.3823 17 12.0063 17C11.5843 17 11.1853 16.8769 10.8662 16.6317C10.8092 16.576 10.5582 16.364 10.3532 16.1638C9.07715 14.9954 6.96905 11.9455 6.33002 10.3414C6.22701 10.1079 6.012 9.48466 6 9.16131C6 8.84967 6.068 8.54879 6.21701 8.25962C6.42202 7.90403 6.74004 7.62561 7.11706 7.46931C7.37907 7.36869 8.16511 7.21336 8.18811 7.21336C8.74914 7.11274 9.53718 7.03751 10.4572 7.00039C10.6222 6.99355 10.7822 7.07659 10.8672 7.21433Z" fill="currentColor"></path>
                    <path opacity="0.4" d="M13.14 7.67228C12.953 7.37041 13.192 6.9904 13.551 7.00505C14.393 7.0412 15.1351 7.10372 15.6871 7.17992C15.6991 7.19164 16.6781 7.34697 17.0092 7.52574C17.6242 7.83737 18.0002 8.44892 18.0002 9.10637V9.16108C17.9892 9.58506 17.6132 10.4867 17.5902 10.4867C17.4012 10.941 17.0812 11.534 16.6961 12.1719C16.5221 12.4591 16.0951 12.466 15.9181 12.1787L13.14 7.67228Z" fill="currentColor"></path>
                </svg>
            </span>
            <span class="item-name">Create a nest</span>
        </a>
    </li>
    @endif
    @foreach ($nests as $nest)
    <li class="nav-item">
        <a class="nav-link" aria-current="page" href="{{ route('nests.index', $nest->name) }}">
            <span class="icon d-flex align-items-center justify-content-center pt-1 m-0" style="width: 20px; min-width: 20px; max-width: 20px; height: 20px;">
                @if($nest->profile_image)
                    <img src="{{ asset('storage/nests/profiles/' . $nest->profile_image) }}" alt="{{ $nest->name }}" style="width: 20px; height: 20px; object-fit: cover; border-radius: 4px; display: block;">
                @else
                    <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.4" d="M16.0756 2H19.4616C20.8639 2 22.0001 3.14585 22.0001 4.55996V7.97452C22.0001 9.38864 20.8639 10.5345 19.4616 10.5345H16.0756C14.6734 10.5345 13.5371 9.38864 13.5371 7.97452V4.55996C13.5371 3.14585 14.6734 2 16.0756 2Z" fill="currentColor"></path>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M4.53852 2H7.92449C9.32676 2 10.463 3.14585 10.463 4.55996V7.97452C10.463 9.38864 9.32676 10.5345 7.92449 10.5345H4.53852C3.13626 10.5345 2 9.38864 2 7.97452V4.55996C2 3.14585 3.13626 2 4.53852 2ZM4.53852 13.4655H7.92449C9.32676 13.4655 10.463 14.6114 10.463 16.0255V19.44C10.463 20.8532 9.32676 22 7.92449 22H4.53852C3.13626 22 2 20.8532 2 19.44V16.0255C2 14.6114 3.13626 13.4655 4.53852 13.4655ZM19.4615 13.4655H16.0755C14.6732 13.4655 13.537 14.6114 13.537 16.0255V19.44C13.537 20.8532 14.6732 22 16.0755 22H19.4615C20.8637 22 22 20.8532 22 19.44V16.0255C22 14.6114 20.8637 13.4655 19.4615 13.4655Z" fill="currentColor"></path>
                    </svg>
                @endif
            </span>
            <span class="item-name" style="text-transform: lowercase">n/{{ $nest->name }}</span>
        </a>
    </li>
    @endforeach
    <li>
        <hr class="hr-horizontal">
    </li>
    
    @php $user = Auth::user(); @endphp
    @if ($user && $user->user_type === 'admin')
    <li class="nav-item static-item">
        <a class="nav-link static-item disabled" href="#" tabindex="-1">
            <span class="default-icon">Superadmin</span>
            <span class="mini-icon">-</span>
        </a>
    </li>
    <li class="nav-item"><a class="nav-link" href="{{ route('superadmin.users') }}"><span class="item-name">Users</span></a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('superadmin.nests') }}"><span class="item-name">Nests</span></a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('superadmin.posts') }}"><span class="item-name">Posts</span></a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('superadmin.comments') }}"><span class="item-name">Comments</span></a></li>
    <li><hr class="hr-horizontal"></li>
    @endif
</ul>
