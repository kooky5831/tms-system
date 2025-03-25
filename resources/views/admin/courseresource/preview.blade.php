@extends('admin.layouts.master')
@section('title', 'Course Resource View')
@push('css')
<style>  
    i.dripicons-trash.trash-icon { color: #ffffff; }
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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Students</a></li>
                        <li class="breadcrumb-item active">Course Resource</li>
                    </ol>
                </div>
                <h4 class="page-title">Course Resource</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div><!--end row-->
    <!-- end page title end breadcrumb -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @foreach($mainCourse as $courseName)
                        <h4>{{$courseName->name}}</h4>
                    @endforeach
                    @if(count($resourcesData) > 0)
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
                                        <a href="{{ url('/storage/course-resources')."/".$resource->resource_file }}" target="_blank" class="download-icon-link d-inline-block btn btn-success btn-sm mr-2" >
                                            <i class="dripicons-download file-download-icon"></i>
                                        </a>
                                        <a href="{{ route('admin.course-resources.edit', $resource->id) }}" class="download-icon-link d-inline-block btn btn-dark btn-sm mr-2">
                                            <i class="dripicons-pencil pencil-icon"></i>
                                        </a>
                                        <a href="{{ route('admin.course-resources.remove-resource', $resource->id) }}" class="download-icon-link d-inline-block btn btn-danger btn-sm mr-2">
                                            <i class="dripicons-trash trash-icon"></i>
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
                            <h4>Courese Resource is curruntly not available</h4>
                            <small class="text-muted">Trainer will be uploaded soon</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div><!-- row -->
</div><!-- container -->
@endsection