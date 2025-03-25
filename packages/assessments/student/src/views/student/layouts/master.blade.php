<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
        <meta charset="utf-8" />
        <title>@yield('title') - {{ env('APP_NAME') }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset('assets/images/favicon-lg.png') }}">

        <!-- <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet"> -->

        <!-- App css -->
        <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/css/metisMenu.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/css/style.css') }}?v={{ config('settings.assetVersion') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/plugins/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />

        <style>
        .ea-student-portal .footer-text a, .ea-student-portal .primary-color, .ea-student-portal .page-title-box .page-title, .ea-student-portal .breadcrumb-item.active, .ea-student-portal .text-primary{ color: #fa5e37 !important; }
        .ea-student-portal .topbar .topbar-left { background-color: #fa5e37; width: 275px; }
        .ea-student-portal .left-sidenav { max-width: 275px; min-width:275px; }
        .ea-student-portal .main-menu-inner { width:275px; background-color: #222222; }
        .ea-student-portal .main-menu-inner .menu-body .nav-item .nav-link.active i, .ea-student-portal .main-menu-inner .menu-body .nav-item .nav-link.active { color: #fa5e37; }
        .ea-student-portal .main-menu-inner .menu-body .nav-item a, .ea-student-portal .main-menu-inner .menu-body .nav-item i { color: #ffffff; }
        .ea-student-portal .main-menu-inner .menu-body .nav-item .nav-link:hover, .ea-student-portal .main-menu-inner .menu-body .nav-item .nav-link:hover i, .ea-student-portal .btn-primary:not(:disabled):not(.disabled):active  { color: #fa5e37; }
        .ea-student-portal .btn-primary { background-color: #fa5e37; }
        .ea-student-portal .assessment .btn-primary { border: 1px solid #fa5e37; }
        .ea-student-portal .assessment .card:hover, .ea-student-portal .btn-success:active { border-color: #fa5e37 !important; }
        .ea-student-portal .badge-success, .ea-student-portal .btn-success { background-color: #28a745; }
        .ea-student-portal .color-full, .ea-student-portal .btn-success:active { background-color: #fa5e37 !important; }
        .ea-student-portal .btn-module, .ea-student-portal .btn-module:active { background-color: #fa5e37; }
        .ea-student-portal .dropdown-menu .dropdown-item:hover, .ea-student-portal .dropdown-menu .dropdown-item.active, .ea-student-portal .dropdown-menu .dropdown-item:active { color:#fa5e37; }
        .ea-student-portal .btn-success { border: 1px solid #28a745; }
        .ea-student-portal .btn-dark { border: 1px solid #fa5e37;} 
        .ea-student-portal .exam-meta, .ea-student-portal .welcome-intro { color: #23365F; font-weight: 600; }
        .ea-student-portal .threeicons { content: url({{ asset('assets/images/menu.png') }})}
        </style>
        @stack('css')

    </head>

    <body class="ea-student-portal">

        @include('assessments::student.layouts.topbar')

        <div class="page-wrapper">

            @include('assessments::student.layouts.sidebar')

            <!-- Page Content-->
            <div class="page-content">
                @yield('content')
                <div class="clearfix"></div>
                @include('assessments::student.layouts.footer')
            </div>
            
            <!-- end page content -->
        </div>
        
        <!-- end page-wrapper -->

        <div class="modal fade model-box" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" id="modal-content">
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <!-- jQuery  -->
        <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/js/metisMenu.min.js') }}"></script>
        <script src="{{ asset('assets/js/waves.min.js') }}"></script>
        <script src="{{ asset('assets/js/jquery.slimscroll.min.js') }}"></script>



        <script src="{{ asset('assets/plugins/moment/moment.js') }}"></script>
        <script src="{{ asset('assets/plugins/toastr/toastr.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>

        <!-- App js -->
        <script src="{{ asset('assets/js/app.js') }}?v={{ config('settings.assetVersion') }}"></script>

        <script type="text/javascript">
            function showToast(msg,type) {
                if( type == 1 ) {
                    toastr["success"](msg, 'Success');
                } else {
                    toastr["error"](msg, 'Error');
                }
            }
        </script>


        @stack('scripts')

    </body>
</html>
