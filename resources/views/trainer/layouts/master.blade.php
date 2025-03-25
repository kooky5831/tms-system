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

        <!-- App css -->
        <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/css/metisMenu.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/css/style.css') }}?v={{ config('settings.assetVersion') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/plugins/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />

        <style>
        .ea-trainer-portal .footer-text a, .ea-trainer-portal .primary-color, .ea-trainer-portal .page-title-box .page-title, .ea-trainer-portal .breadcrumb-item.active, .ea-trainer-portal .text-primary{ color: #23365F !important; }
        .ea-trainer-portal .topbar .topbar-left { background-color: #23365F; width: 275px; }
        .ea-trainer-portal .left-sidenav { max-width: 275px; min-width:275px; }
        .ea-trainer-portal .main-menu-inner { width:275px; background-color: #ffffff; }
        .ea-trainer-portal .main-menu-inner .menu-body .nav-item .nav-link.active i, .ea-trainer-portal .main-menu-inner .menu-body .nav-item .nav-link.active { color: #E35F4A; }
        .ea-trainer-portal .main-menu-inner .menu-body .nav-item a, .ea-trainer-portal .main-menu-inner .menu-body .nav-item i { color: #23365F; }
        .ea-trainer-portal .main-menu-inner .menu-body .nav-item .nav-link:hover, .ea-trainer-portal .main-menu-inner .menu-body .nav-item .nav-link:hover i  { color: #E35F4A; }
        .ea-trainer-portal .btn-primary { background-color: #E35F4A; color:#ffffff; }
        .ea-trainer-portal .assessment .btn-primary { border: 1px solid #23365F; }
        .ea-trainer-portal .assessment .card:hover, .ea-trainer-portal .btn-success:active { border-color: #23365F !important; }
        .ea-trainer-portal .badge-success, .ea-trainer-portal .btn-success, .ea-trainer-portal .btn-secondary { background-color: #23365F !important; }
        .ea-trainer-portal .color-full, .ea-trainer-portal .btn-success:active, .ea-trainer-portal .btn-secondary:hover, .ea-trainer-portal .btn-secondary:focus, .ea-trainer-portal .btn-secondary:active, .ea-trainer-portal .select2-container--default .select2-selection--multiple .select2-selection__choice, .ea-trainer-portal .select2-container--default .select2-results__option--highlighted[aria-selected], .ea-trainer-portal .daterangepicker td.active, .ea-trainer-portal .daterangepicker td.active:hover { background-color: #23365F !important; }
        .ea-trainer-portal .btn-module, .ea-trainer-portal .btn-module:active, .ea-trainer-portal .pagination .page-item.active .page-link, .ea-trainer-portal .reapet-add { background-color: #23365F !important; }
        .ea-trainer-portal .dropdown-menu .dropdown-item:hover, .ea-trainer-portal .dropdown-menu .dropdown-item.active, .ea-trainer-portal .dropdown-menu .dropdown-item:active, .ea-trainer-portal .custom-file-label { color:#23365F; }
        .ea-trainer-portal .btn-success, .ea-trainer-portal .custom-file-label, .ea-trainer-portal .custom-file-input:focus, .ea-trainer-portal .btn-secondary { border: 1px solid #23365F; }
        .ea-trainer-portal .btn-dark, .ea-trainer-portal .pagination .page-item.active .page-link { border: 1px solid #23365F;} 
        .ea-trainer-portal .exam-meta, .ea-trainer-portal .welcome-intro { color: #23365F; font-weight: 600; }
        .ea-trainer-portal .threeicons { content: url({{ asset('assets/images/menu_trainer.png') }})}
        .ea-trainer-portal .btn-primary:hover {background-color: #E35F4A !important; border: 1px solid #E35F4A;}
        .ea-trainer-portal .add-new { content: url({{ asset('assets/images/add_course_white_trainer.png') }})}
        .ea-trainer-portal .list-dots { content: url({{ asset('assets/images/three-dots_trainer.png') }})}
        .ea-trainer-portal .date-ico:after { content: url({{ asset('assets/images/calendar_trainer.png') }}) }
        .ea-trainer-portal .btn-secondary:active, .secondary:not(:disabled):not(.disabled):active, .btn-secondary:not(:disabled):not(.disabled):active { background-color: #23365F !important; border: 1px solid #23365F; }
        .ea-trainer-portal .btn-primary:not(:disabled):not(.disabled):active { color: #23365F !important; }
        .ea-trainer-portal .btn-primary:focus, .ea-trainer-portal .btn-primary:not(:disabled):not(.disabled):active{ color:#23365F; }
        </style>

        @stack('css')

    </head>

    <body class="ea-trainer-portal">

        @include('trainer.layouts.topbar')

        <div class="page-wrapper">

            @include('trainer.layouts.sidebar')

            <!-- Page Content-->
            <div class="page-content">
                @yield('content')
                <div class="clearfix"></div>
                <footer class="footer text-center ">
                    <div class="footer-text">Copyright &copy; <?php echo date("Y"); ?> | Designed & Developed by <a href="/">Equinet Academy Private Limited</a> </div>
                </footer><!--end footer-->
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
        <script src="{{ asset('assets/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>

        <!-- App js -->
        <script src="{{ asset('assets/js/app.js') }}"></script>
        <script type="text/javascript">

            $(document).ready(function() {
                $(document).on('click', '#generate_student_assessment', function(e){
                    e.preventDefault();
                    var courseRunId = $(this).attr('data-courserunId');
                    if(courseRunId){
                        swal.fire({
                            title: 'Are you sure?',
                            text: "You want to generate the assessment!",
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes',
                            cancelButtonText: 'No',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.value) {
                                $.ajax({
                                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                                    url: '{{ route('trainer.dashboard.generate_assess') }}',
                                    type: "POST",
                                    dataType: "JSON",
                                    data: {
                                        courseRunId: courseRunId
                                    },
                                    success: function(res) {
                                        if( res.status == true ) {
                                            swal.fire(
                                                'Completed!',
                                                'Assessment generated successfully.',
                                                'success'
                                            )
                                            location.reload();
                                        } else {
                                            swal.fire(
                                                'Opps',
                                                'There are no student available at a moment in this course run.',
                                                'error'
                                            )
                                        }
                                    }
                                }); // end ajax
                            }
                        });
                    }
                });
            });

            function isNumberKey(evt) {
                var charCode = (evt.which) ? evt.which : evt.keyCode;
                if (charCode > 31
                    && (charCode < 48 || charCode > 57))
                    return false;

                return true;
            }

            function isNumberKeyWithDecimal(evt)
            {
                var charCode = (evt.which) ? evt.which : evt.keyCode;
                if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
                    return false;
                return true;
            }

            function initTooltip(){
                $.fn.tooltip && $('[data-toggle="tooltip"]').tooltip()
                $('[data-toggle="tooltip-custom"]').tooltip({
                    template: '<div class="tooltip tooltip-custom" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                });
            }

            function readURL(input,id) {

                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        $('#'+id).removeClass('d-none');
                        $('#'+id).attr('src', e.target.result);
                    }

                    reader.readAsDataURL(input.files[0]);
                }
            }

            // initialize toaster
            toastr.options = {
                "closeButton": true,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": true,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
            };
            // toastr["success"]("Have fun storming the castle!","Success");
            // toastr["error"]("Have fun stoming the castle!", 'Error');
            function showToast(msg,type) {
                if( type == 1 ) {
                    toastr["success"](msg, 'Success');
                } else {
                    toastr["error"](msg, 'Error');
                }
            }

            @if(Session::has('notify-success'))
                toastr["success"]("{{ Session::get('notify-success') }}", 'Success');
            @endif
            @if(Session::has('notify-error'))
                toastr["error"]("{{ Session::get('notify-error') }}", 'Error');
            @endif

        </script>
        @stack('scripts')

    </body>
</html>
