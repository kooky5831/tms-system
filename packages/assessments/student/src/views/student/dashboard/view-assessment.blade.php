@extends('assessments::student.layouts.master')
@section('title', 'Student Enrolment List')
@push('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/fancybox/jquery.fancybox.min.css') }}" rel="stylesheet" type="text/css" />

    <style>
        .fontsize-15{ font-size:15px; }  
        .primary-color { color: #658bf7; font-weight: 500; }
        .assessment .card { height: 100%; border-radius: 10px; overflow: hidden; border: 1px solid rgba(154, 170, 207, 0.1); transition: border 0.1s, transform 0.3s; } 
        .assessment .card .card-body { padding-bottom:75px; }
        .assessment .btn-primary { min-width: 150px; border: 1px solid #6673fd; padding: 10px; }
        .assessment .card:hover { border-color: #6673fd; -webkit-transform: translateY(-10px); transform: translateY(-10px); }
        .assessment .card .card-footer { position: absolute; left: 20px; bottom: 30px; background-color: transparent; padding: 15px 0 0; }
        .assessment .card .card-footer .badge { min-width: 150px; line-height: 26px; padding: 10px; }
        .question-wrapper .list-group .collapse-module button.btn-module { position: absolute; right: 20px; top: 12px; z-index: 1; }
        .question-wrapper .list-group .list-group-item { padding: 0.5rem 1.25rem; }
        .all-check {float: right;color: #fff;}
        .question-wrapper .list-group .collapse-module {width: 97%;}
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row assessment" id="assessment">
        <div class="w-100 d-flex align-items-center justify-content-between mb-4">
            <div class="float-left">
                <h3>{{$getAssessmentName->title}}</h3>
            </div>
        </div>
        <div class="col-12 question-wrapper disabled-content">
            @php $count = 0 @endphp
            @if($previewExam->count() > 0)
            <ul class="list-group">
                @foreach ($previewExam as $preview)
                @php $count++ @endphp
                <li class="list-group-item mb-3">
                    <div class="collapse-module">
                        <h5 class="mb-3">Question {{$count}}: {{$preview->question}}</h5>
                        <button class="btn btn-success btn-dark btn-module que_{{$preview->id}}" type="button" data-toggle="collapse" data-target="#que_{{$preview->id}}" aria-expanded="false" aria-controls="collapseExample" onclick="collapseToggle({{$preview->id}})"><i class="fas fa-minus"></i></button>

                        @if($preview->questionImages->count() > 0)
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="question">Question Image:</label>
                                </div>
                                <div class="image_preview row" style="display: inline-block;">
                                    @foreach($preview->questionImages as $images)
                                        <a href="{{asset('storage/images/question-images/'.$images->question_image)}}" data-fancybox="group">
                                            <div class="image-field-{{$images->id}} position-relative float-left col-lg-4">
                                                <img class='b'  src='{{asset('storage/images/question-images/'.$images->question_image)}}' style='width:100%; float: left;'>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="module-form collapse show" id="que_{{$preview->id}}">
                        <form class="exam-forms frm_index_{{$preview->id}}" enctype="multipart/form-data">
                                <input type="hidden" name="question_id" value="{{$preview->id}}" class="question_id" id="question_{{$preview->id}}">
                                {{-- <input type="hidden" name="course_run_id" value="{{$preview->course_id}}" class="course_run_id" id="course_run_{{$preview->id}}"> --}}
                                <input type="hidden" name="exam_id" value="{{$preview->exam_id}}" class="exam_id" id="exam_{{$preview->id}}">
                                <input type="hidden" name="student_enr_id" value="{{$preview->student_id}}" class="student_id" id="student_{{$preview->id}}">
                                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            @if($getAttachment->count() > 0)
                                            <label for="question">Attachment:</label>
                                            @foreach($getAttachment as $attachment)
                                                <div>
                                                    @if($preview->answer_que_id == $attachment->question_id)
                                                        <a href="{{asset('storage/assesment-submission/answer-documents/'.$attachment->student_enrol_id."/".$attachment->assessment_id."/".$attachment->question_id."/".$attachment->submission_attchment)}}">{{$attachment->submission_attchment}}</a>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        @php
                                            $answer_value = "";
                                            if($preview->answer_que_id == $preview->id){
                                                $answer_value = $preview->submitted_answer;
                                            }
                                        @endphp
                                        <label for="template_text_{{$preview->id}}" class="lbl_answer">Answer <span class="text-danger">*</span> <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Please give your answer below."></i></label>
                                        <textarea id="template_text_{{$preview->id}}" name="template_text" class="form-control h-auto template_text" rows="8" disabled> {{$answer_value}}</textarea>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </li>
                    @endforeach
            </ul>
            @else
                    <div class="row">
                        <div class="col-md-12">
                        </div>
                    </div>
            @endif  
                <div class="all-check">
                    <a href="{{route('student.assessment.dashboard')}}" class="btn btn-info" id="save_assessment">Cancel</a>
                </div>        
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/ckeditor5/ckeditor.js') }}" ></script>
    <script src="{{ asset('assets/plugins/fancybox/jquery.fancybox.min.js') }}" ></script>
    <script type="text/javascript">

            $('[data-fancybox]').fancybox({
                // Options will go here
                buttons : [
                    'close'
                ],
                wheel : false,
                transitionEffect: "slide",
                thumbs          : true,
                hash            : true,
                loop            : true,
                keyboard        : true,
                toolbar         : true,
                animationEffect : true,
                arrows          : true,
                clickContent    : true
            }); 

            document.querySelectorAll('.template_text').forEach(function(templateText){
                ClassicEditor
                    .create(templateText,  {
                    }).then(editor => {
                        editor.enableReadOnlyMode("editor");
                    }).catch(error => { console.error(error)})
            })

        function collapseToggle(data_id){
            $('.que_'+data_id).find('i').toggleClass('fas fa-minus fas fa-plus');
        }
    </script>
@endpush