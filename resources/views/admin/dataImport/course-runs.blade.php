@extends('admin.layouts.master')
@section('title', 'Data Import - Course Runs')
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
                        <li class="breadcrumb-item active">Course Runs</li>
                    </ol>
                </div>
                <h4 class="page-title">Course Runs</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h2 class="text-title">Data Import - Course Runs</h2>

                    <form method="POST" action="{{route('admin.dataImport.courseRun')}}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="coursemain">Course</label>
                                    <select name="coursemain" id="coursemain" required class="form-control select2">
                                        @foreach ($courseMainList as $coursemain)
                                            <option value="{{$coursemain->id}}">{{$coursemain->name}} ( {{$coursemain->reference_number}})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="import_file">Upload File</label>
                                    <div class="custom-file">
                                        <input type="file" name="import_file" class="custom-file-input" id="import_file">
                                        <label class="custom-file-label mt-2" for="import_file">Choose File</label>
                                        @error('import_file')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <h4>For Importing the data. Please follow below workflow:</h4>
                                <ol>
                                    <li>Download the file <a class="ml-3 btn btn-info btn-sm" href="/assets/data-import/importTemplate(CourseRun&Enrolment).xlsx" download><i class="fa fa-download"></i></a></li>
                                    <li>Create a new excel document, pasting the column labels into the new document</li>
                                    <li>Add course run details and save</li>
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
