@extends('admin.layouts.master')
@section('title', 'Reports - Course Runs Signups')
@push('css')
<link href="{{ asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
@endpush
@section('content')
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Reports</a></li>
                        <li class="breadcrumb-item active">Course Runs Signups</li>
                    </ol>
                </div>
                <h4 class="page-title">Course Runs Signups</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{route('admin.reports.courseRunsSignups.export.excel')}}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="coursemain">Course</label>
                                    <select name="coursemain[]" id="coursemain" multiple class="form-control select2">
                                        <option value="">Select Course</option>
                                        @foreach ($courseMainList as $coursemain)
                                            <option value="{{$coursemain->id}}">{{$coursemain->name}} ( {{$coursemain->reference_number}})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            {{-- <div class="col-md-3">
                                <div class="form-group">
                                    <label for="courserun">Course Run</label>
                                    <select name="courserun" id="courserun" class="form-control select2">
                                        <option value="">Select Course Run</option>
                                    </select>
                                </div>
                            </div> --}}
                            <div class="col-md-3">
                                <div class="form-group date-ico">
                                    <label for="startDate">Start Date</label>
                                    <input type="text" id="startDate" autocomplete="new-password" name="startDate" class="form-control" value="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group date-ico">
                                    <label for="endDate">End Date</label>
                                    <input type="text" id="endDate" autocomplete="new-password" name="endDate" class="form-control" value="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-1">
                                <button class="btn btn-primary mt-4" type="submit">Export Excel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div><!-- container -->
@endsection

@push('scripts')
<script src="{{ asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script type="text/javascript">
    $(function () {
        $(".select2").select2({ width: '100%' });

        $('#startDate').datepicker({
            format: 'mm/dd/yyyy',
            clearBtn: true,
            autoclose: true,
            todayHighlight: true
        })
        .change(startDateChanged);
        // .on('changeDate', startDateChanged);

        $('#endDate').datepicker({
            format: 'mm/dd/yyyy',
            clearBtn: true,
            autoclose: true,
            todayHighlight: true,
        });

        function startDateChanged(ev) {
            // $('#endDate').datepicker('destroy');
            // do something, like clearing an input
            let minDate = new Date($('#startDate').val());
            $('#endDate').val('');
            $('#endDate').datepicker('setStartDate', minDate);
        }

        /*$(document).on('change', '#coursemain', function(e) {
            e.preventDefault();
            let _coursemain = $(this).val();
            if( _coursemain != "" ) {
                // get the course run list for this courses
                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    url: '{{ route('admin.ajax.reports.courserun.list') }}',
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        id: _coursemain
                    },
                    success: function(res) {
                        if( res.status ) {
                            $('#courserun').empty();
                            if( res.list.length > 0 ) {
                                $('#courserun').append(new Option('Select Course Run', ''));
                                res.list.map((course) => {
                                    $('#courserun').append(new Option(`${course.tpgateway_id} (${course.course_start_date})`, course.id));
                                });
                            }
                        }
                    },
                    error: function(err) {
                        if( err.status == 422 ) {
                            // display error
                            showToast(err.responseJSON.message, 0);
                            return false;
                        }
                    }
                }); // end ajax
            }
        });*/

    });
</script>
@endpush
