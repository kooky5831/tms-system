@extends('admin.layouts.master')
@section('title', 'Create Invoice')
@section('content')
<link rel="stylesheet" href="{{asset('assets/css/invoice.css')}}">
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body invoice-head"> 
                <div class="row">
                    <div class="col-md-4 align-self-center">
                        <img src="{{asset('storage/invoice-image/'.$pdfData['invoice_setting']['invoice_logo'])}}" alt="logo-small" class="auth-logo"class="logo-sm mr-2" height="100"/>
                    </div>
                    <div class="col-md-8">
                        <ul class="list-inline mb-0 contact-detail float-right">
                            <li class="list-inline-item">
                                <div class="pl-3">
                                    <i class="mdi mdi-map-marker"></i>
                                    <h4 style="color:#e24d36;">Equinet Academy Private Limited</h4>
                                    <p>International Plaza 10 Anson Road,<br />
                                    #25-08 SINGAPORE 079903<br />
                                    SINGAPORE</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div><!--end card-body-->
            <div class="card-body xero-invoice">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="">
                            <h6 class="my-0 font-poppins"><b class="text-dark-blue">Invoice Date :</b> {{$pdfData['dates']['invoice_date']}}</h6>
                            <h6 class="font-poppins"><b class="text-dark-blue">Due Date: </b> {{$pdfData['dates']['due_date']}}  </h6>
                            <h6 class="font-poppins"><b class="text-dark-blue">Invoice Number : </b>{{$pdfData['invoice_data']['invoice_number']}}</h6>
                        </div>
                    </div>
                    <div class="col-md-6">                                            
                        <div class="float-left">
                            <address class="font-13 font-poppins">
                                <strong class="font-14 fw-700 text-dark-blue">Billed To : </strong>
                                {{$pdfData['comapany']['comapany_uen']}}<br>
                                {{$pdfData['comapany']['comapany_name']}}<br>
                                {{$pdfData['comapany']['billing_address']}}
                            </address>
                            <address class="font-13 font-poppins">
                                <strong class="font-14 fw-700 text-dark-blue">Billing Email : </strong>
                                {{$pdfData['comapany']['company_email']}}
                            </address>
                        </div>
                    </div>
                    
                    <div class="col-md-3 font-poppins">
                        <b class="font-poppins text-dark-blue">Equinet Academy Private Limited</b><br>
                        <b class="font-poppins text-dark-blue">GST Registration Number:</b><br>
                        201708981D
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr class="text-center bg-dark font-poppins">
                                        <th>Quantity</th>
                                        <th>Description</th>
                                        <th>Unit price</th>
                                        <th>Tax</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pdfData['itemlist'] as $value)
                                        <tr class="text-center">
                                            @if(strtolower($value['description']) == 'ssg training grant' && $value['line_amount'] == 0)
                                            @elseif(strtolower($value['description']) == 'ssg training grant (baseline funding)' && $value['line_amount'] == 0)
                                            @elseif(strtolower($value['description']) == 'ssg training grant (enhanced subsidy)' && $value['line_amount'] == 0)
                                            @else
                                                <td>{{$value['description']}}</td>
                                                <td>{{$value['quantity']}}</td>
                                                <td>@money($value['unit_amount'])</td>
                                                <td>@money($value['tax_amount'])</td>
                                                <td>@money($value['line_amount'])</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                        <tr>
                                            <td colspan="3" class="border-0"></td>
                                            <td class="border-0 font-14 text-dark-blue"><b>Sub Total</b></td>
                                            <td class="border-0 font-14 text-dark-blue"><b>@money($pdfData['amounts']['sub_total'])</b></td>
                                        </tr>
                                        <tr>
                                            <th colspan="3" class="border-0"></th>
                                            <td class="border-0 font-14 text-dark-blue"><b>Total GST</b></td>
                                            <td class="border-0 font-14 text-dark-blue"><b>@money($pdfData['amounts']['total_tax'])</b></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="border-0"></td>
                                            <td class="border-0 font-14 text-dark-blue"><b>Invoice Total SGD</b></td>
                                            <td class="border-0 font-14 text-dark-blue"><b>@money($pdfData['amounts']['invoice_total_sgd'])</b></td>
                                        </tr>
                                        <tr>
                                            <th colspan="3" class="border-0"></th>
                                            <td class="border-0 font-14 text-dark-blue"><b>Total Net Payment SGD</b></td>
                                            <td class="border-0 font-14 text-dark-blue"><b>@money($pdfData['amounts']['total_net_payment_sgd'])</b></td>
                                        </tr>
                                        <tr class="bg-dark text-white">
                                            <th colspan="3" class="border-0"></th>
                                            <td class="border-0 font-14 text-white "><b>Amount Due SGD </b></td>
                                            <td class="border-0 font-14 text-white"><b>@money($pdfData['amounts']['amount_sgd'])</b></td>
                                        </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-12 col-12">
                        {!! $pdfData['invoice_setting']['payment_terms'] !!}
                    </div>
                </div>
                <div class="row justify-content-center align-items-center">
                    <div class="col-lg-10 col-12">
                        {!! $pdfData['invoice_setting']['payment_methods'] !!}
                    </div>
                    <div class="col-lg-2 col-12">
                        <div class="payment-barcode">
                            <img src="{{asset('storage/invoice-image/'.$pdfData['invoice_setting']['invoice_qr'])}}" alt="qr_code" height="150" class="float-right"/>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row d-flex justify-content-center ">
                    <div class="col-lg-12 col-xl-4 ml-auto align-self-center">
                        <div class="text-center text-muted"><small>Thank you very much for doing business with us. Thanks !</small></div>
                    </div>
                    <div class="col-lg-12 col-xl-4">
                        <div class="float-right d-print-none">
                            <a href="{{route('admin.course.createinvoice', $pdfData['invoice_data']['invoice_id'])}}" class="btn btn-success">Download</a>
                            @if($pdfData['invoice_data']['xero_sync'] == 1)
                                <a href="{{route('admin.xero.update.xeroinvoice', $pdfData['invoice_data']['xero_invoice_id'])}}"  class="btn btn-success">Sync TMS to Xero</a> 
                            @else
                                <a href="{{route('admin.xero.generate.xeroinvoice', $pdfData['invoice_data']['student_enroll_id'])}}" class="btn btn-success">Sync with xero</a> 
                            @endif
                            <a href="#" class="btn btn-warning">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div><!--end card-->
    </div><!--end col-->
</div><!--end row-->
@endsection