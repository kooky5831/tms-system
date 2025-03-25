<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <title>Login - {{ config('app.name') }}</title>
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
                                    <a href="javascript:void(0)" class="logo logo-admin"><img src="{{ asset('assets/images/Equinet-Academy-Logo.svg') }}" height="55" alt="logo" class="auth-logo"></a>
                                </div><!--end auth-logo-box-->

                                <div class="text-center auth-logo-text">
                                    <h4 class="mt-0 mb-3 mt-5">Let's Get Started</h4>
                                    {{-- <p class="text-muted mb-0">Sign in to continue to TMS.</p> --}}
                                </div> <!--end auth-logo-text-->

                                @if(session('errors'))
                                <div class="col-md-12 alert alert-danger">
                                    <ul>
                                        @foreach(session('errors')->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif


                                <form class="form-horizontal auth-form my-4" method="POST" action="{{ route('login') }}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="username">NRIC or ID No.</label>
                                        <div class="input-group mb-1">
                                            <span class="auth-form-icon">
                                                <i class="dripicons-user"></i>
                                            </span>
                                            <input type="text" class="form-control" id="username" name="username" placeholder="Enter NRIC or ID No." value="{{ old('username') }}" required autofocus>
                                        </div>
                                        @if ($errors->has('username'))
                                        <div class="ml-3 text-danger"><p>{{ $errors->first('username') }}</p></div>
                                        @endif
                                    </div><!--end form-group-->

                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <div class="input-group mb-1">
                                            <span class="auth-form-icon">
                                                <i class="dripicons-lock"></i>
                                            </span>
                                            <input type="password" class="form-control" id="password" name="password" required placeholder="Enter password">
                                        </div>
                                        <small class="text-muted">Please enter your NRIC/FIN/Passport No. (ALL CAPS) as your password.</small>
                                    </div><!--end form-group-->

                                    <div class="form-group row mt-4">
                                        <div class="col-sm-6">
                                            <div class="custom-control custom-switch switch-success">
                                                <input type="checkbox" name="remember" class="custom-control-input" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                                <label class="custom-control-label text-muted" for="remember">Remember me</label>
                                            </div>
                                        </div><!--end col-->
                                        {{-- <div class="col-sm-6 text-right">
                                            <a href="{{ route('password.request') }}" class="text-muted font-13"><i class="dripicons-lock"></i> Forgot password?</a>
                                        </div><!--end col--> --}}
                                    </div><!--end form-group-->

                                    <div class="form-group mb-0 row">
                                        <div class="col-12 mt-2">
                                            <button class="btn btn-primary btn-round btn-block waves-effect waves-light" type="submit">Log In <i class="fas fa-sign-in-alt ml-1"></i></button>
                                        </div><!--end col-->
                                    </div> <!--end form-group-->
                                </form><!--end form-->
                            </div><!--end /div-->

                            {{-- <div class="m-3 text-center text-muted">
                                <p class="">Don't have an account ?  <a href="javascript:void(0)" class="text-primary ml-2">Free Register</a></p>
                            </div> --}}
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
