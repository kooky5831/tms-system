<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <title>Forgot Password - {{ config('app.name', 'Krivi') }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset('assets/images/favicon-lg.png') }}">

        <!-- App css -->
        <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/css/metisMenu.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/css/style.css') }}?v={{ config('settings.assetVersion') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/plugins/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />

    </head>

    <body class="account-body accountbg">
        <!-- Log In page -->
        <div class="row vh-100 ">
            <div class="col-12 align-self-center">
                <div class="auth-page">
                    <div class="card auth-card shadow-lg">
                        <div class="card-body">
                            <div class="px-3">
                                <div class="auth-logo-box">
                                    <a href="javascript:void(0)" class="logo logo-admin"><img src="{{ asset('assets/images/favicon-lg.png') }}" height="55" alt="logo" class="auth-logo"></a>
                                </div><!--end auth-logo-box-->

                                <div class="text-center auth-logo-text">
                                    <h4 class="mt-0 mb-3 mt-5">Reset Password</h4>
                                    <p class="text-muted mb-0">Enter your new password.</p>
                                </div> <!--end auth-logo-text-->

                                @if (\Session::has('success'))
                                <div class="alert alert-success">
                                    <ul>
                                        <li>{!! \Session::get('success') !!}</li>
                                    </ul>
                                </div>
                                @endif

                                @if(session('errors'))
                                <div class="col-md-12 alert alert-danger">
                                    <ul>
                                        @foreach(session('errors')->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif

                                @if (session('status'))
                                    <div class="col-md-12 alert alert-success" role="alert">
                                        {{ session('status') }}
                                    </div>
                                @endif


                                <form class="form-horizontal auth-form my-4" method="POST" action="{{ route('password.update') }}">
                                    @csrf
                                    <input type="hidden" name="token" value="{{ $token }}">
                                    <div class="form-group">
                                        <label for="username">NRIC or ID No.</label>
                                        <div class="input-group mb-1">
                                            <span class="auth-form-icon">
                                                <i class="dripicons-mail"></i>
                                            </span>
                                            <input type="text" class="form-control" id="username" name="username" placeholder="Enter email" value="{{ $username ?? old('username') }}" autocomplete="username" required>
                                        </div>
                                        @error('username')
                                        <div class="ml-3 text-danger"><p>{{ $message }}</p></div>
                                        @enderror
                                    </div><!--end form-group-->

                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <div class="input-group mb-1">
                                            <span class="auth-form-icon">
                                                <i class="dripicons-lock"></i>
                                            </span>
                                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required autofocus autocomplete="current-password">
                                        </div>
                                        @error('password')
                                        <div class="ml-3 text-danger"><p>{{ $message }}</p></div>
                                        @enderror
                                    </div><!--end form-group-->

                                    <div class="form-group">
                                        <label for="password-confirm">Password</label>
                                        <div class="input-group mb-1">
                                            <span class="auth-form-icon">
                                                <i class="dripicons-lock"></i>
                                            </span>
                                            <input type="password" class="form-control" id="password-confirm" name="password_confirmation" placeholder="Confirm Password" required autofocus autocomplete="new-password">
                                        </div>
                                        @error('password_confirmation')
                                        <div class="ml-3 text-danger"><p>{{ $message }}</p></div>
                                        @enderror
                                    </div><!--end form-group-->


                                    <div class="form-group mb-0 row">
                                        <div class="col-12 mt-2">
                                            <button class="btn btn-primary btn-round btn-block waves-effect waves-light" type="submit">{{ __('Reset Password') }} <i class="fas fa-sign-in-alt ml-1"></i></button>
                                        </div><!--end col-->
                                    </div> <!--end form-group-->
                                </form><!--end form-->
                            </div><!--end /div-->

                            <div class="m-3 text-center text-muted">
                                <p class="">Remember It ?  <a href="{{ route('login') }}" class="text-primary ml-2">Login In </a></p>
                            </div>
                        </div><!--end card-body-->
                    </div><!--end card-->

                </div><!--end auth-page-->
            </div><!--end col-->
        </div><!--end row-->
        <!-- End Log In page -->


        <!-- jQuery  -->
        <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/js/metisMenu.min.js') }}"></script>
        <script src="{{ asset('assets/js/waves.min.js') }}"></script>
        <script src="{{ asset('assets/js/jquery.slimscroll.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/toastr/toastr.min.js') }}"></script>

        <!-- App js -->
        <script src="{{ asset('assets/js/app.js') }}?v={{ config('settings.assetVersion') }}"></script>
        <script src="{{ asset('assets/js/coreapp.js') }}"></script>

    </body>
</html>
