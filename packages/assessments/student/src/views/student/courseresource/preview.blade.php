@extends('assessments::student.layouts.master')
@section('title', 'Course Resource View')
@push('css')
<style>
    .file-box{border: 1px solid #eff2f9;
    border-radius: 5px;
    padding: 20px;
    width: 300px;
    display: inline-block;
    margin-right: 5px;
    margin-bottom: 16px;
    background-color: #ffffff;}
    .file-box {
        border: 1px solid #eff2f9;
        border-radius: 5px;
        padding: 20px;
        width: 100%;
        display: inline-block;
        margin-right: 5px;
        margin-bottom: 16px;
        background-color: #ffffff;
    }

    .file-box h6 { font-size: 22px; margin: 20px 0px 10px;  }

    .file-box .file-box-header { display: flex; flex-direction: row; justify-content: space-between; margin-bottom: 30px; }

    .file-box .file-box-header .file-box-header-inner { display: flex; gap: 15px }

    .file-box .file-box-header .file-box-header-inner a { font-size: 18px; }
    .file-box .file-box-header .file-box-header-inner a i.dripicons-trash.trash-icon { color: red; }
    .paginate {float: right;margin-top: 30px;margin-right: 12px }
    .paginate{ float: right; margin: 40px 12px 0px 0px; }
</style>
@endpush
@section('content')
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">{{config('app.name')}}</a></li>
                        <li class="breadcrumb-item active">Course Resource</li>
                    </ol>
                </div>
                <h4 class="page-title">Course Resource: {{ $courseMainName->name }}</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div><!--end row-->
    <!-- end page title end breadcrumb -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if(count($resourcesData) > 0)
                        <div class="col-12 d-flex justify-content-end pr-5">
                            <a href="{{route('student.course-resources.download-all-resource', $id)}}" class="float-right">
                                <button type="button" class="btn btn-success">Download All</button> 
                            </a>
                        </div>
                        <div class="col-12">
                            <ul class="list-group">
                                @foreach($resourcesData as $resource)
                                    <li class="list-group-item w-100 mt-3 d-flex align-items-center">
                                        <label class="customfile">
                                            {!! getFileExtension($resource->resource_file) !!}
                                        </label>
                                        <div class="task-info">
                                            <h4>{{ $resource->resource_title }}</h4>
                                            <strong class="d-block mt-2">
                                                {{-- <div class="d-inline-block text-muted">Valid up to 3 Years of course run start date</div> --}}
                                            </strong>
                                        </div>
                                        <span class="btn-icon-group-style d-flex justify-content-end">
                                            <a href="{{ url('/storage/course-resources')."/".$resource->resource_file }}" target='_blank' class="float-right">
                                                <button type="button" class="btn btn-success">Download</button> 
                                            </a>
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="paginate float-right mt-4">
                            {!! $resourcesData->links() !!}
                        </div>
                    @else
                        <div>
                            <h4>Course Resource is currently not available</h4>
                            {{-- <small class="text-muted">Trainer will be uploaded soon</small> --}}
                        </div>
                    @endif       
                </div>
            </div>
        </div>
    </div><!-- container -->
</div>


@endsection