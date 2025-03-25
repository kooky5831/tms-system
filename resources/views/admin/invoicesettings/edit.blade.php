@extends('admin.layouts.master')
@section('title', 'Invoice Settings')
@section('content')
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Invoice Setting</a></li>
                        <li class="breadcrumb-item active">Setting</li>
                    </ol>
                </div>
                <h4 class="page-title">Invoice Setting</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{route('admin.invoicesettings.set.settings')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <h4 class="header-title mt-0">Basic Details</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Invoice logo <span class="text-danger">*</span></label>
                                    <div class="custom-file">
                                        <input type="file" accept="image/*" name='invoice_logo' class="custom-file-input" id="invoice_logo" value="{{ asset('storage/invoice-image/' . $invoiceSettingData['invoice_logo']) }}">
                                        <label class="custom-file-label" for="invoice_logo">Choose File</label>
                                        @error('invoice_logo')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                    <img src='{{ asset('storage/invoice-image/' . $invoiceSettingData['invoice_logo']) }}' id="invoice_image_logo" width="200px" style="margin-top: 25px" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Payment QR<span class="text-danger">*</span></label>
                                    <div class="custom-file form-group">
                                        <input type="file" accept="image/*" name='payment_qr' class="custom-file-input" id="payment_qr">
                                        <label class="custom-file-label" for="payment_qr">Choose File</label>
                                        @error('payment_qr')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                    <img src='{{ asset('storage/invoice-image/' . $invoiceSettingData['invoice_qr']) }}' id="payment_qr_logo" width="200px" style="margin-top: 25px" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Payment Terms<span class="text-danger">*</span></label>
                                    <textarea id="payment_terms" name='payment_terms' class="form-control h-auto" rows="8" required>
                                        {{$invoiceSettingData['payment_terms']}}
                                    </textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Payment Methods<span class="text-danger">*</span></label>
                                    <textarea id="payment_methods" name='payment_methods' class="form-control h-auto" rows="8" required>
                                        {{$invoiceSettingData['payment_methods']}}
                                    </textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Invoice address<span class="text-danger">*</span></label>
                                    <textarea id="invoice_address" name='invoice_address' class="form-control h-auto" rows="8" required>
                                        {{$invoiceSettingData['invoice_address']}}
                                    </textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer m-0 clearfix">
                        <button type="submit" class="btn btn-primary mar-r-10">Submit</button>
                        <a href="#" class="btn btn-danger">Cancel</a>
                    </div>
                </form>
            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->
</div><!-- container -->
@endsection
@push("scripts")
{{-- <script src="{{ asset('assets/plugins/ckeditor4/ckeditor.js') }}" ></script> --}}
<script src="https://cdn.ckeditor.com/4.11.2/full/ckeditor.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        CKEDITOR.replace( 'payment_terms' );
        CKEDITOR.replace( 'payment_methods' );
        CKEDITOR.replace( 'invoice_address' );
        CKEDITOR.config.allowedContent = true;
    });
    
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            var customId = $(input).attr('id');
            if(customId == "invoice_logo"){
                reader.onload = function (e) {
                    $('#invoice_image_logo').attr('src', e.target.result);
                }
            }else if(customId == "payment_qr"){
                reader.onload = function (e) {
                    $('#payment_qr_logo').attr('src', e.target.result);
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#invoice_logo").change(function(){
        readURL(this);
    });
    $("#payment_qr").change(function(){
        readURL(this);
    });
</script>
@endpush