@extends('admin.layouts.master')
@section('title', 'Data Import - Student Enrolment')
@section('content')
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Data Import</a></li>
                        <li class="breadcrumb-item active">Student Enrolment</li>
                    </ol>
                </div>
                <h4 class="page-title">Student Enrolment</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h2 class="text-title">Data Import - Student Enrolment</h2>

                    <form method="POST" action="{{route('admin.dataImport.studentEnrolment')}}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            {{-- <div class="col-md-6">
                                <div class="form-group">
                                    <label for="courserun">Course Run</label>
                                    <select name="courserun" id="courserun" required class="form-control select2">
                                        @foreach ($courseList as $course)
                                            <option value="{{$course->id}}">{{$course->course_start_date}} - {{$course->course_end_date}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> --}}

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="import_file">Upload File</label>
                                    <div class="input-group mb-3">
                                        <input type="file" name="import_file" id="import_file" class="form-control w-100">                  
                                        @error('import_file')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                    <!-- <div class="custom-file">
                                        <input type="file" name="import_file" class="custom-file-input" id="import_file">
                                        <label class="custom-file-label mt-2" for="import_file">Choose File</label>
                                        @error('import_file')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div> -->
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <h4>For Importing the data. Please follow below workflow:</h4>
                                <ol>
                                    <li>Download the file <a class="ml-3 btn btn-info btn-sm" href="/assets/data-import/importTemplate(CourseRun&Enrolment).xlsx" download><i class="fa fa-download"></i></a></li>
                                    <li>Create a new excel document, pasting the column labels into the new document</li>
                                    <li>Add student details and save</li>
                                    <li>Upload the new document</li>
                                </ol>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-1">
                                <button class="btn btn-primary mt-4" type="submit">Import Data</button>
                            </div>
                        </div>
                    </form>
                    @if (\Session::has('smerrors'))
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5>Errors While inserting</h5>
                            <ul class="list-group">
                                @foreach( \Session::get('smerrors') as $err )
                                    <li class="list-group-item">{{$err['column_no']}} - {{$err['message']}}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div><!-- container -->
@endsection
@push('scripts')
<script type="text/javascript">
    $(function () {
        $(".select2").select2({ width: '100%' });
    });
</script>
@endpush
