@extends('admin.layouts.master')
@section('title', 'Reports - Course Run Export')
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
                        <li class="breadcrumb-item active">Course Run Export</li>
                    </ol>
                </div>
                <h4 class="page-title">Course Run Export</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{route('admin.reports.courseRuns.export.excel')}}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="coursemain">Course</label>
                                    <select name="coursemain[]" id="coursemain" multiple class="form-control select2" placeholder="Select course">
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
                            {{-- <div class="col-md-3">
                                <div class="form-group date-ico">
                                    <label for="startDate">Start Date</label>
                                    <input type="text" id="startDate" autocomplete="new-password" name="from" class="form-control" value="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group date-ico">
                                    <label for="endDate">End Date</label>
                                    <input type="text" id="endDate" autocomplete="new-password" name="to" class="form-control" value="">
                                </div>
                            </div> --}}
                        </div>
                        <div class="row">
                            <div class="col-md-2">
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
        $(".select2").select2({ width: '100%', placeholder: "Select Course" });

        /* $('#startDate').datepicker({
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
        });*/

        function startDateChanged(ev) {
            // $('#endDate').datepicker('destroy');
            // do something, like clearing an input
            let minDate = new Date($('#startDate').val());
            $('#endDate').val('');
            $('#endDate').datepicker('setStartDate', minDate);
        }

    });
</script>
@endpush
