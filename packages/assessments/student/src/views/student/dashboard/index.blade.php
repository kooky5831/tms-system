@extends('assessments::student.layouts.master')
@section('title', 'Student Enrolment List')
@push('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

    <style>
        .exam-item { padding: 12px; }
        .fontsize-15 { font-size:15px; }
        .primary-color { color: #658bf7; font-weight: 500; }
        .assessment .card { height: 100%; border-radius: 10px; overflow: hidden; border: 1px solid rgba(154, 170, 207, 0.1); transition: border 0.1s, transform 0.3s; } 
        .assessment .card .card-body { padding-bottom:75px; }
        .assessment .btn-primary { min-width: 150px; border: 1px solid #6673fd; padding: 10px; }
        .assessment .card:hover { border-color: #6673fd; -webkit-transform: translateY(-10px); transform: translateY(-10px); }
        .assessment .card .card-footer { position: absolute; left: 20px; bottom: 30px; background-color: transparent; padding: 15px 0 0; }
        .assessment .card .card-footer .badge { min-width: 150px; line-height: 26px; padding: 10px; }
        .center {display: block;margin-left: auto;margin-right: auto;width: 50%;}

        #feedback-modal.modal, .modal-backdrop { width: calc(100% - 275px); left: initial; right: 0px; }

        .feedback { margin-bottom: 20px; }
        .feedback .card { width: 100%; margin: 0 auto; border-radius: 10px; }
        #feedback-modal .modal-body .instructions-list p { font-size: 14px;}
        .feedback .card-footer { background-color: transparent; margin: 0 auto; }
        .intro-heading { text-align: center; }
        #feedback-modal .modal-body > div { width: 100%; }
        #feedback-modal .modal-body .feedback-inner { min-width: 500px; background-color: #FFFFFF; border-radius: 10px; padding: 60px 40px 60px; }
        #feedback-modal .modal-body { max-width: 1000px; width: 95%; height: calc(100vh - 57px); margin: 0 auto; display: flex; align-items: center; justify-content: center; }
        #feedback-modal .modal-body .intro-heading { font-size: 28px; font-weight: 700; }

        #feedback-modal .modal-body .back-to-link { position: absolute; top: 20px; left: 60px; font-size: 16px; font-weight: 500; color: #454545; cursor: pointer; }
        #feedback-modal .modal-body .back-to-link svg { height: 15px; fill: #454545; transition: all 0.5s ease; }
        #feedback-modal .modal-body .back-to-link:hover svg { fill: #fa5e37; transform: translateX(-5px); }
        #feedback-modal .modal-body .back-to-link:hover { color: #fa5e37; }

        #feedback-modal .modal-body .upload-form .custom-file-label { margin: 20px auto; }

        #feedback-modal .modal-body .upload-form .form-group { margin-bottom: 50px; }
        #feedback-modal .modal-body .upload-form .form-group h4 { font-size: 24px; font-weight: 700; color: #454545; margin-bottom: 30px; }

        #feedback-modal .modal-body .upload-form .custom-file-label { width: 200px; }

 
    @media only screen and (max-width: 991px) { 
        #feedback-modal .modal-body { width: 95%; }
        #feedback-modal .modal-body .feedback-inner { padding: 30px; }
        #feedback-modal .modal-body .instructions-list { padding: 0px; margin-left: 0px; }
    }

    @media only screen and (max-width: 767px) { 
        #feedback-modal .modal-body .intro-heading { font-size: 28px; line-height: 34px; }
    }
        
    </style>
@endpush
@section('content')
<div class="container-fluid">
    <div class="row assessment">
        {{-- @dump($traineeFeedback) --}}
        {{-- @foreach($traineeFeedback as $key => $value)
            @if($value >= 2)
            @php 
               $data =  explode('-', $key); 
            @endphp
                <input type="hidden" class="popup" value="{{$data['1']}}" studentisfinished="{{$data['1']}}" id="popup_{{$data['0']}}_{{$value}}">
            @endif
        @endforeach --}}
        <input type="hidden" value="" id="student_enr_id">
        @if($examtData->count() > 0)
            @foreach($examtData as $exam)
                @if($exam->is_assigned == 1)
                    <div class="col-3 exam-item">
                        <div class="card shadow">
                            <!-- <img src="{{  Storage::url('public/course-images/course-default.png') }}" class="card-img-top" alt="{{ $exam->name }}"> -->
                            <div class="card-body card-p">
                                <h3 class="card-title">{{ $exam->name }}: {{ $exam->assessment_name }}</h3>
                                <p>Your exam date is <strong>{{ $exam->course_end_date }}</strong> and starting time is <strong>{{ Carbon\Carbon::parse($exam->assessment_time)->format('g:i a' ) }} </strong></p>
                                <div class="row mt-4">
                                    <div class="col-6 col-xs-6 py-2 fontsize-15 exam-meta">
                                        <i class="far fa-calendar"></i> {{ $exam->date_option == 1 ?  $exam->course_start_date . " to " .  $exam->course_end_date : $exam->course_end_date}}
                                    </div>
                                    <div class="col-6 col-xs-6 py-2 fontsize-15 exam-meta">
                                        <i class="fas fa-stopwatch"></i> {{ $exam->assessment_duration }}
                                    </div>
                                    <div class="col-6 col-xs-6 py-2 fontsize-15 exam-meta">
                                        <i class="far fa-clock"></i> {{ Carbon\Carbon::parse($exam->assessment_time)->format('g:i a' ) }}
                                    </div>
                                </div>
                                @if(!is_null($exam->is_passed))
                                    <div class="row">
                                        <div class="col-6 col-xs-6 py-2 fontsize-15 exam-meta">
                                            <span>Assessment Outcome: </span>
                                            
                                        </div>
                                        <div class="col-6 col-xs-6 py-2 fontsize-15 exam-meta">
                                            <span class="outcome" style="color:{{$exam->is_passed == 'c' ? '#3cb758' : '#f1646c'}}">{{ syncAssessmentWithTrainer($exam->is_passed) }} </span>
                                        </div>
                                    </div>
                                @endif
                                <div class="card-footer">
                                    @php $courseDatePast =  Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $exam->course_end_date . " 23:59:59")->isPast()  @endphp 
                                    @if(($exam->is_finished == 1 || $courseDatePast) && $exam->trainee_view_access == 1)
                                        <a href="{{route('student.assessment.exam.preview', ['assessmentId' => $exam->assessment_id, 'studentId' => $exam->student_id]) }}" class="btn btn-primary">View</a>
                                    @elseif($exam->is_finished != 1)
                                    <a href="" class="btn btn-primary exam_rules" data-courseid="{{ $exam->course_id }}" data-assessmentid="{{ $exam->assessment_id }}" data-studentid="{{ $exam->student_id }}" data-examid="{{ $exam->exam_id }}">Start</a>
                                    @else
                                        <span class="badge badge-success text-white">Exam Submitted</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        @else
            <div class="row">
                <div class="col-md-12">
                    <h3> There are no assessments available at the moment.</h3>
                </div>
            </div>
        @endif 
    </div>
</div>
<input type="hidden" value="{{$studentEnrolment}}" id="student_data">
<div class="ajax-loader"><div class="loader-center"><div class="tms_loader"></div></div></div>
@include('assessments::student.dashboard.feedback-modal')
{{-- <div class="modal fade" id="exampleModal" data-bs-backdrop='static' tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
            <h5 class="modal-title text-center mb-3" style="font-size: x-large;" id="exampleModalLabel">Feedback</h5>

            <p style="text-align: center;font-size: 14px; color:#000;">Congratulations on successfully completing the course! Your feedback is invaluable in helping us improve. Please take a moment to scan the following TRAQOM QR code to submit your feedback. You may locate the Course Run ID on the whiteboard or obtain it from the trainer. Thank you for helping us enhance your learning experience.</p>

            <img src="{{ asset('storage/feedback-qr-code/TRAQOM_QR.png') }}" class="mt-2 mb-3 center" style=" width: 75%;">
            
            <div class="text-center">
                <button class="btn btn-primary feedback mt-3" value="" id="feedback">Feedback Submitted</button>
            </div>
            </div>
        </div>
    </div>
</div> --}}
@endsection

@push('scripts')
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>

    <script>

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            var customId = $(input).attr('id');
            reader.onload = function (e) {
                $('#feedback_image').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#screenshot").change(function(){
        readURL(this);
        $('#feedback_image').show();
    });

        $(document).on('click', '.exam_rules', function(e) {
            e.preventDefault();
            var examID = $(this).data('examid');
            var courseId = $(this).data('courseid');
            var studentId = $(this).data('studentid');
            var assessmentId = $(this).data('assessmentid');
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('student.assessment.ajax.examrules.modal') }}',
                type: "POST",
                data: {
                    courseId: courseId,
                    examID: examID,
                    studentId: studentId,
                    assessmentId: assessmentId,
                },
                dataType: "JSON",
                success: function(res) {
                    $('#modal-content').empty().html(res.html);
                    $('.model-box').modal();
                }
            }); // end ajax
        });

        //comment code of popup
        // $(document).ready(function(){  
        //     document.querySelectorAll('.popup').forEach(function(check){
        //         console.log(check.value);
        //         console.log($('.feedback').val(check.value));
                
        //         console.log($("#"+$(check).find('.feedback').attr('id')).val());
        //         $('#exampleModal').modal('show');
        //         // $('.feedback')
        //         $("#exampleModal").modal({
        //             backdrop: 'static',
        //             keyboard: false
        //         });
        //     })
        // });
        // $(document).on('click', '.feedback', function(e){
        //     e.preventDefault();
        //     var studentEnrolId = $(this).val();
        //     $.ajax({
        //         headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        //         url: '{{ route('student.assessment.feedback') }}',
        //         type: "POST",
        //         data: {
        //             studentEnrolId: studentEnrolId
        //         },
        //         dataType: "JSON",
        //         success: function(res) {
        //             showToast(res.msg, 1);
        //             setTimeout(() => {
        //                 window.location.replace('{{ route('student.assessment.dashboard') }}');
        //             }, 2000);
        //             console.log(res);
        //         }
        //     })
        // });
        $(document).on('click', '.upload-ss', function(e){
            e.preventDefault();
            // var file_data = $('#screenshot').prop('files')[0];
            var checkedSurvey = $('input.multiCheck').prop('checked') ? 1 : 0;
            if(checkedSurvey){
                var studentEnrolId = $('#student_enr_id').val();
                var formdata = new FormData();
                // formdata.append('file', file_data);
                formdata.append('is_feedback_submitted', checkedSurvey);
                formdata.append('enrolment_id', studentEnrolId);
                $.ajax({
                    headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    url: '{{ route('student.assessment.feedback.screenshot') }}',
                    type: "POST",
                    data: formdata,
                    cache : false,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $(".ajax-loader").show();
                    },
                    complete: function(){
                        $(".ajax-loader").hide();
                    },
                    success: function(res) {
                        $(".ajax-loader").hide();
                        if(res.status == 'success'){
                            swal.fire(
                                'Thank You',
                                'Your feedback has been submitted.',
                                'success'
                            )
                            $('#feedback-modal').removeClass('show');
                            $('.modal-backdrop').removeClass('show');
                        } else {
                            showToast("Please select checkbox", 0);
                        }
                    }
                })
            } else {
                showToast("Please select checkbox", 0);
            }
        });

        $(document).on('click', '.link-btn', function(e){
            $('.link-form').css('display', 'none');
            $('.upload-form').css('display', 'block');
        })
        $(document).on('click', '.back-to-link', function(e){
            $('.upload-form').css('display', 'none');
            $('.link-form').css('display', 'block');
        })

        $(document).ready(function(){
            var data = jQuery.parseJSON($('#student_data').val());
            $.ajax({
                headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: '{{ route('student.assessment.feedback.getdata') }}',
                type: "POST",
                data: data,
                dataType: "json",
                success: function(res) {
                    if(res.status == 'success') {
                        $.each(res.data, function(key, value) {
                            const startDateTime   = new Date(value);
                            const currentDateTime = new Date();
                            const endDateTime     = new Date(value);
                            const removeMinutes = new Date(value);
                            
                            removeMinutes.setMinutes(removeMinutes.getMinutes() - 30);
                            let timeString = "23:59:59";
                            let [hours, minutes, seconds] = timeString.split(':').map(Number);
                            endDateTime.setHours(hours, minutes, seconds);

                            var pop_up_start_time  = removeMinutes.getTime();
                            var currentTime        = currentDateTime.getTime();
                            var pop_up_end_time    = endDateTime.getTime();
                            
                            if (currentTime >= pop_up_start_time && currentTime <= pop_up_end_time) {
                                $('#student_enr_id').val(data[key]);
                                $('#feedback-modal').modal('show');
                            }
                        });
                    }
                }
            })
        })
    </script>
@endpush