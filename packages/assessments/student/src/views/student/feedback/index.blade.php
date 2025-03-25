@extends('assessments::student.layouts.master')
@section('title', 'Feedback')
@section('content')
    <div class="container-fluid">
         <!-- Page-Title -->
         <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <h4 class="page-title">Feedback</h4>
                </div><!--end page-title-box-->
            </div><!--end col-->
        </div><!--end row-->

        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body card-p">
                        <h3 class="card-title text-center">Scan this QR code and submit your feedback</h3>
                        <div class="d-flex align-items-center justify-content-center">
                            <img src="{{ asset('storage/feedback-qr-code/'.$feedbackSettingData['feedback_qr']) }}" class="mt-2 mb-3 center" style=" width: 25%;">
                        </div>
                        {!! $feedbackSettingData['feedback_text'] !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection