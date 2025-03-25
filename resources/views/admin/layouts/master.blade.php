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
        .ea-admin-portal .topbar .topbar-left { width: 275px; }
        .ea-admin-portal .left-sidenav { max-width: 275px; min-width:275px; }
        .ea-admin-portal .main-menu-inner { width:275px; }
        </style>
        @stack('css')

    </head>

    <body class="ea-admin-portal">

        @include('admin.layouts.topbar')

        <div class="page-wrapper">

            @include('admin.layouts.sidebar')

            <!-- Page Content-->
            <div class="page-content">
                @yield('content')
                <div class="clearfix"></div>
                <footer class="footer">
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
        <script src="{{ asset('assets/js/app.js') }}?v={{ config('settings.assetVersion') }}"></script>
        <script type="text/javascript">

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
                        $('#canvas').css('background-image', 'url(' + e.target.result + ')');
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

            $(document).ready(function() {
                @can('course-add')
                $(document).on('click', '#add_course_run_menu, #add_course_run_btn', function(e) {
                    e.preventDefault();
                    $.ajax({
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        url: '{{ route('admin.ajax.getcoursermain.modal.list') }}',
                        type: "POST",
                        dataType: "JSON",
                        success: function(res) {
                            $('#modal-content').empty().html(res.html);
                            $('.model-box').modal();
                        }
                    }); // end ajax
                });
                @endcan

                $(document).on('click', '.updatetasknote', function(e) {
                    e.preventDefault();
                    let _task_id = $(this).attr('task_id');
                    $.ajax({
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        url: '{{ route('admin.ajax.admintask.modal.getupdatenotes') }}',
                        type: "POST",
                        dataType: "JSON",
                        data: {
                            id: _task_id
                        },
                        success: function(res) {
                            $('#modal-content').empty().html(res.html);
                            $('.model-box').modal();
                        }
                    }); // end ajax
                });

                $(document).on('submit', '#task_note_form', function(e) {
                    e.preventDefault();
                    var btn = $('#submit_task_note_edit');
                    BITBYTE.progress(btn);
                    $.ajax({
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        url: '{{ route('admin.ajax.admintask.modal.updatenotes') }}',
                        type: "POST",
                        dataType: "JSON",
                        data: new FormData(this),
                        contentType: false,
                        cache: false,
                        processData:false,
                        success: function(res) {
                            BITBYTE.unprogress(btn);
                            showToast(res.message, res.success);
                            if( res.success ) {
                                setTimeout(function() {
                                    location.reload();
                                },1000);
                            }
                        },
                        error: function(err) {
                            BITBYTE.unprogress(btn);
                            if( err.status == 422 ) {
                                // display error
                                showToast(err.responseJSON.message, 0);
                                return false;
                            }
                        }
                    }); // end ajax
                });

                // view the task details
                $(document).on('click', '.viewtaskdetails', function(e) {
                    e.preventDefault();
                    let _task_id = $(this).attr('task_id');
                    $.ajax({
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        url: '{{ route('admin.ajax.admintask.modal.gettaskdetails') }}',
                        type: "POST",
                        dataType: "JSON",
                        data: {
                            id: _task_id
                        },
                        success: function(res) {
                            $('#modal-content').empty().html(res.html);
                            $('.model-box').modal();
                        }
                    }); // end ajax
                });

                // mark task as completed start
                $(document).on('click', '.marktaskComplete', function () {
                    let _task_id = $(this).attr('task_id');
                    swal.fire({
                        title: 'Are you sure?',
                        text: "You want to mark this task as completed!",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, complete it!',
                        cancelButtonText: 'No',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.value) {
                            $.ajax({
                                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                                url: '{{ route('admin.ajax.admintask.modal.markTaskCompleted') }}',
                                type: "POST",
                                dataType: "JSON",
                                data: {
                                    id: _task_id
                                },
                                success: function(res) {
                                    if( res.status == true ) {
                                        swal.fire(
                                            'Completed!',
                                            'Your task has been completed.',
                                            'success'
                                        )
                                        location.reload();
                                    } else {
                                        swal.fire(
                                            'Opps',
                                            'Some error occured, Please try again.',
                                            'error'
                                        )
                                    }
                                }
                            }); // end ajax
                        }
                    });
                });
                // mark task as completed end


                //mark task as uncomplete start
                $(document).on('click', '.marktaskUncomplete', function () {
                    let _task_id = $(this).attr('task_id');
                    swal.fire({
                        title: 'Are you sure?',
                        text: "You want to mark this task as uncompleted!",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, uncompleted it!',
                        cancelButtonText: 'No',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.value) {
                            $.ajax({
                                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                                url: '{{ route('admin.ajax.admintask.modal.marktaskUncomplete') }}',
                                type: "POST",
                                dataType: "JSON",
                                data: {
                                    id: _task_id
                                },
                                success: function(res) {
                                    if( res.status == true ) {
                                        swal.fire(
                                            'Completed!',
                                            'Your task removed from the completed.',
                                            'success'
                                        )
                                        location.reload();
                                    } else {
                                        swal.fire(
                                            'Opps',
                                            'Some error occured, Please try again.',
                                            'error'
                                        )
                                    }
                                }
                            }); // end ajax
                        }
                    });
                });
                //mark task as uncomplete finish

                //mark all complete start
                $(document).on('click', '.mark-complete-all', function(){
                    var taskIds = [];
                    $.each($("input[name='oldIds']:checked"), function(){
                        taskIds.push($(this).val());
                    });
                    if(taskIds.length != 0){
                        swal.fire({
                            title: 'Are you sure?',
                            text: "You want to mark these tasks as completed!",
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, complete it!',
                            cancelButtonText: 'No',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.value) {
                                $.ajax({
                                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                                    url: '{{ route('admin.ajax.admintask.modal.markAllTaskCompletere') }}',
                                    type: "POST",
                                    dataType: "JSON",
                                    data: {
                                        ids: taskIds
                                    },
                                    success: function(res) {
                                        if( res.status == true ) {
                                            swal.fire(
                                                'Completed!',
                                                'Your task has been completed.',
                                                'success'
                                            )
                                            location.reload();
                                        } else {
                                            swal.fire(
                                                'Opps',
                                                'Some error occured, Please try again.',
                                                'error'
                                            )
                                        }
                                    }
                                }); // end ajax
                            }
                        });
                    } else {
                        toastr.error("Please select atleast one task", "Error")
                    }
                })
                $(document).on('click', '.mark-today-complete-all', function(){
                    var taskIds = [];
                    $.each($("input[name='newIds']:checked"), function(){
                        taskIds.push($(this).val());
                    });
                    if(taskIds.length != 0){
                        swal.fire({
                            title: 'Are you sure?',
                            text: "You want to mark these tasks as completed!",
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, complete it!',
                            cancelButtonText: 'No',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.value) {
                                $.ajax({
                                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                                    url: '{{ route('admin.ajax.admintask.modal.markAllTaskCompletere') }}',
                                    type: "POST",
                                    dataType: "JSON",
                                    data: {
                                        ids: taskIds
                                    },
                                    success: function(res) {
                                        if( res.status == true ) {
                                            swal.fire(
                                                'Completed!',
                                                'Your task has been completed.',
                                                'success'
                                            )
                                            location.reload();
                                        } else {
                                            swal.fire(
                                                'Opps',
                                                'Some error occured, Please try again.',
                                                'error'
                                            )
                                        }
                                    }
                                }); // end ajax
                            }
                        });    
                    } else {
                        toastr.error("Please select atleast one task", "Error")
                    }
                })

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
                                    url: '{{ route('admin.assessments.examdashboard.generate_assess') }}',
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
                })
                //mark all complete end

            });
        </script>
        @stack('scripts')

    </body>
</html>
