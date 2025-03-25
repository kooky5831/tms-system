@extends('admin.layouts.master')
@section('title', 'Payment')
@push('css')
<!-- DataTables -->
<link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item"><a href="{{route('admin.payment.list')}}">Xero Theme Setting</a></li>
                        <li class="breadcrumb-item active">Settings</li>
                    </ol>
                </div>
                <h4 class="page-title">Xero Theme Setting</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{route('admin.xero.set-xero-theme')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row space-between marlr0">
                            <h4 class="header-title mt-0">Xero Theme Setting</h4>
                            <div class="form-group" style="float: right;">
                                <a href="{{route('admin.xero.get-xero-themes')}}" id="setting_form" class="btn btn-primary">Get Themes From The Xero</a>
                            </div>
                        </div>
                        @php $count = 0 @endphp
                        @foreach($brandingThemes as $key => $theme)
                            <div class="row"> 
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="brand_theme_name">Brand Theme name</label>
                                        <input type="text" class="form-control" value="{{ $theme->name }}" name="brand_theme_data[{{$key}}][name]" id="name" placeholder="">
                                        <input type="hidden" value="{{$theme->branding_theme_id}}" name="brand_theme_data[{{$key}}][branding_theme_id]">
                                    </div>
                                </div>                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="applied_on">Applied on</label>
                                        <select class="form-control select2" id="applied_on" name="brand_theme_data[{{$key}}][applied_on]">
                                            <option>Select type</option>
                                            <option value="self-sponsored" @selected("self-sponsored" == $theme->applied_on)>Self Sponsored</option>
                                            <option value="comany-sponsored" @selected("comany-sponsored" == $theme->applied_on)>Company Sponsored</option>
                                        </select>
                                    </div>
                                </div>    
                            </div>
                        @endforeach
                    </div><!--end card-body-->
                    <div class="card-footer m-0 clearfix">
                        <button type="submit" class="btn btn-primary mar-r-10">Update</button>
                    </div>
                </form>
            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->
</div>
@endsection
@push('scripts')
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>

<script type="text/javascript">
    $(".select2").select2({width: '100%'});
</script>
@endpush