<!-- Top Bar Start -->
<div class="topbar">

    <!-- LOGO -->
    <div class="topbar-left">
        <a href="{{ route('student.assessment.dashboard') }}" class="logo">
            <span>
                <img src="{{ asset('assets/images/Equinet-Academy-Logo.svg') }}" alt="{{config('app.name')}}" class="logo-sm">
            </span>
            {{-- <span>
                <img src="{{ asset('assets/images/logo-dark.png') }}" alt="{{config('app.name')}}" class="logo-lg">
            </span> --}}
        </a>
    </div>
    <!--end logo-->
    <!-- Navbar -->
    <nav class="navbar-custom">
        <ul class="list-unstyled topbar-nav mb-0">
            <li>
                <button class="button-menu-mobile nav-link waves-effect waves-light">
                   <!--  <i class="dripicons-menu nav-icon"></i> -->
                   <i class="threeicons nav-icon"></i>
                </button>
            </li>
        </ul>
        @if(getStudentNRIC(auth()->user()->id))
             <h3 class="welcome-intro">Welcome {{getStudentNRIC(auth()->user()->id)}} </h3>
        @else
            <h3 class="welcome-intro">Welcome to TMS Assessment Portal </h3>
        @endif
        <ul class="list-unstyled topbar-nav float-right mb-0">

            <li class="dropdown">
                <a class="nav-link dropdown-toggle waves-effect waves-light nav-user" data-toggle="dropdown" href="#" role="button"
                    aria-haspopup="false" aria-expanded="false">
                    <img src="{{ Storage::url(config('uploadpath.user_profile_storage') . '/' . 'default.jpg') }}" alt="profile-user" class="rounded-circle" />
                    <span class="ml-1 nav-user-name hidden-sm"> <i class="mdi mdi-chevron-down"></i> </span>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="dripicons-exit text-muted mr-2"></i> Logout</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    {{-- <a class="dropdown-item" href="{{route('student.logout')}}" ><i class="dripicons-exit text-muted mr-2"></i> Logout</a> --}}
                </div>
            </li>
        </ul><!--end topbar-nav-->

    </nav>
    <!-- end navbar-->
</div>
<!-- Top Bar End -->
