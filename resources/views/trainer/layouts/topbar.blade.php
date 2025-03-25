<!-- Top Bar Start -->
<div class="topbar">

    <!-- LOGO -->
    <div class="topbar-left">
        <a href="{{ route('trainer.dashboard') }}" class="logo">
            <span>
                <img src="{{ asset('assets/images/Equinet-Academy-Logo.svg') }}" alt="{{config('app.name')}}" class="logo-sm">
            </span>
        </a>
    </div>
    <!--end logo-->
    <!-- Navbar -->
    <nav class="navbar-custom">
        <ul class="list-unstyled topbar-nav mb-0">
            <li>
                <button class="button-menu-mobile nav-link waves-effect waves-light">
                   <i class="threeicons nav-icon"></i>
                </button>
            </li>
        </ul>
        <ul class="list-unstyled topbar-nav float-right mb-0">

            <li class="dropdown">
                <a class="nav-link dropdown-toggle waves-effect waves-light nav-user" data-toggle="dropdown" href="#" role="button"
                    aria-haspopup="false" aria-expanded="false">
                    <img src="{{ Storage::url(config('uploadpath.user_profile_storage') . '/' . Auth::user()->profile_avatar) }}" alt="profile-user" class="rounded-circle" />
                    <span class="ml-1 nav-user-name hidden-sm">{{ Auth::user()->name }} <i class="mdi mdi-chevron-down"></i> </span>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="{{ route('trainer.profile') }}"><i class="dripicons-user text-muted mr-2"></i> Profile</a>
                    <a class="dropdown-item" href="{{ route('trainer.changepassword') }}"><i class="dripicons-lock text-muted mr-2"></i> Change Password</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="dripicons-exit text-muted mr-2"></i> Logout</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </li>
        </ul><!--end topbar-nav-->

    </nav>
    <!-- end navbar-->
</div>
<!-- Top Bar End -->
