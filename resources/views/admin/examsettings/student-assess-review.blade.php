@extends('admin.layouts.master')
@section('title', 'Grade Assessment')
@push('css')
<!-- DataTables -->
<link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    .question-wrapper .list-group .collapse-module button.btn-module { position: absolute; right: 20px; top: 10px; z-index: 1;  }
    .question-wrapper .list-group .collapse-module button.btn-module.collapse { position: absolute; right: 20px; top: 50%; z-index: 1; transform: translateY(-50%); }
    .question-wrapper .list-group .list-group-item { padding: 0.5rem 1.25rem; }

    .exam-collaps .checkmark { height: 38px; width: 40px; top: 2px; }
    
    .list-group .list-group-item .exam-collaps { align-items: flex-start; }
    .exam-collaps .customcheck .checkmark:after { left: 17px; top: 10px; }
    .list-group .list-group-item .exam-collaps { padding-right: 10px; }
    .list-group .list-group-item .exam-collaps h5 { width: 75%; }

    .allcustomcheck { width: 18%;  }
    .allcustomcheckparent .allcustomcheck .allcheckmark { top: 7px; left: 0; }
    .recovery-items .recovery-item-inner { display: flex; gap: 50px; align-items: flex-end; }
    .recovery-items .recovery-field { width: 30%; float: left; }
    .recovery-items .recovery-field h5 { margin-top: 0px; }
    .recovery-items .mark-assessment { width: 30%; float: left; display: flex; flex-direction: row-reverse; justify-content: space-between; align-items: flex-end; text-align: end; }
    .recovery-items .all-check { width: 24%; float: right; }
    .recovery-items .select2.select2-container { width: 20% !important; text-align: left; }
    .recovery-items .recovery-item-inner .btn.btn-info { padding: 12px 60px; }

    .allcustomcheckparent { display: flex; flex-direction: row-reverse; width: 20%; }

</style>
@endpush
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Grade Assessment</a></li>
                        <li class="breadcrumb-item active">List</li>
                    </ol>
                </div>
                <h4 class="page-title">Grade Assessment</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <div class="row">
        <div class="col-12">
            <a href="{{ route('admin.assessments.examdashboard.view_trainees', $studentData->course_id) }}" class="btn btn-primary btn-info mb-4 float-lg-right">Back</a>
            <a href="{{ route('admin.assessments.exam-settings.get_pdf_assessment', ["assessmentID" => $assessmentId, "studentEnrolId" => $studentData->id]) }}" class="btn btn-primary btn-info mr-2 mb-4 float-lg-right" target="_blank"> <i class="fa fa-download"></i> &nbsp; Download Assessment</a>
        </div>
        <div class="mt-2 col-12 text-right pr-5">
            <div class="form-check-inline my-1">
                <div class="custom-control custom-radio">
                    <input type="radio" id="passed_all" name="passed_all" class="custom-control-input">
                    <label class="custom-control-label" for="passed_all">All Pass</label>
                 </div>
            </div>
            <div class="form-check-inline my-1">
                <div class="custom-control custom-radio">
                    <input type="radio" id="failed_all" name="passed_all" class="custom-control-input">
                    <label class="custom-control-label" for="failed_all">All Fail</label>
                </div>
            </div>
        </div>
    </div>
    <div class="row assessment" id="assessment">
        <div class="col-12 question-wrapper disabled-content">
            @if(count($allStudentQuestionAns) > 0)
                @php $count = 0 @endphp
                <ul class="list-group">
                    @foreach ($allStudentQuestionAns as $review)
                    @php $count++ @endphp
                    <li class="list-group-item mb-3">
                        <div class="collapse-module">
                            <div class="exam-collaps">
                                <label class="customcheck">
                                    <input type="checkbox" class="multiReview" name="submission_ids" value="{{$review->submission_id}}" 
                                    @if(!empty($review->is_reviewed)) 
                                        checked 
                                    @endif>
                                    <span class="checkmark"></span>
                                </label>
                                <h5 class="mb-3">Question {{$count}}: {{$review->question}}</h5>
                                <div class="mr-5 mt-2">
                                    <div class="form-check-inline my-1">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="is_passed_{{$review->id}}" name="is_passed[{{$review->submission_id}}]" value="1" class="custom-control-input" {{($review->is_pass == 'c') ? 'checked' : ""}}>
                                            <label class="custom-control-label" for="is_passed_{{$review->id}}">Pass</label>
                                         </div>
                                    </div>
                                    <div class="form-check-inline my-1">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="is_failed_{{$review->id}}" name="is_passed[{{$review->submission_id}}]" value="0" class="custom-control-input" {{($review->is_pass == 'nyc') ? 'checked' : ""}}>
                                            <label class="custom-control-label" for="is_failed_{{$review->id}}">Fail</label>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-success btn-dark btn-module que_{{$review->id}}" type="button" data-toggle="collapse" data-target="#que_{{$review->id}}" aria-expanded="false" aria-controls="collapseExample" onclick="collapseToggle({{$review->id}})"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="module-form collapse " id="que_{{$review->id}}">
                            <form class="exam-forms frm_index_{{$review->id}}" enctype="multipart/form-data">
                                    <input type="hidden" name="question_id" value="{{$review->id}}" class="question_id" id="question_{{$review->id}}">
                                    <input type="hidden" name="submitted_assessment_id" value="{{$assessmentId}}" class="submitted_assessment_id" id="exam_{{$review->id}}">
                                    <input type="hidden" name="student_enr_id" value="{{$review->student_enr_id}}" class="student_enr_id" id="student_{{$review->id}}">
                                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                @if($getStudentAttachment->count() > 0)
                                                <label for="question">Student Attachment:</label>
                                                @foreach ($getStudentAttachment as $attachment)
                                                    <div>
                                                        @if($review->answer_que_id == $attachment->question_id)
                                                            <a href="{{asset('storage/assesment-submission/answer-documents/'.$attachment->student_enrol_id."/".$attachment->assessment_id."/".$attachment->question_id."/".$attachment->submission_attchment)}}">{{$attachment->submission_attchment}}</a>
                                                        @endif
                                                    </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <div class="image_preview row" id="image_preview_0" style="display: inline-block;">   
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            @php
                                                $answer_value = "";
                                                if($review->answer_que_id == $review->id){
                                                    $answer_value = $review->submitted_answer;
                                                }
                                            @endphp
                                            <label for="template_text_{{$review->id}}" class="lbl_answer">Answer <span class="text-danger">*</span> <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Please give your answer below."></i></label>
                                            <textarea id="template_text_{{$review->id}}" name="template_text" class="form-control h-auto template_text" rows="8" disabled> {{$answer_value}}</textarea>
                                        </div>
                                    </div>
                                </form>
                        </div>
                    </li>
                    @endforeach
                </ul>
                <div class="row mt-2 recovery-items">
                    <div class="col-12 recovery-item">
                        <div class="recovery-item-inner">
                            <div class="allcustomcheckparent">
                                <h3>Mark All Questions</h3>
                                <label class="allcustomcheck">
                                    <input type="checkbox" class="multiCheck" name="" value="">
                                    <span class="allcheckmark"></span>
                                </label>
                            </div>
                            <div class="recovery-field">
                                <h5>Recovery (If Applicable):</h5>
                                <textarea class="form-control" name="assessment_recovery" id="assessment_recovery">{{$review->assessment_recovery}}</textarea>
                            </div>
                            <div class="mark-assessment">
                                <select class="form-control select2" id="markAssessment" >
                                    <option value="c"{{$review->is_passed == 'c' ? ' selected' : ''}}>C</option>
                                    <option value="nyc"{{$review->is_passed == 'nyc' ? ' selected' : ''}}>NYC</option>
                                    <option value="reschedule"{{$review->is_passed == 'reschedule' ? ' selected' : ''}}>Assessment Returned</option>
                                </select>
                                <textarea class="form-control mt-2" name="assessment_reschedule_note" id="assessment_reschedule_note" style="width: 50%;" placeholder="Add Remarks">{{ $review->assessment_reschedule_note }}</textarea>
                            </div>
                            <div class="all-check">
                                <button class="btn btn-info" id="save_assessment">Save Assessment Result</button>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <h3 style="text-align: center">Assessment is not submitted by Trainee.</h3>
            @endif
        </div>
    </div>
</div>
@endsection
@push('scripts')
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/dropify/js/dropify.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/ckeditor5/ckeditor.js') }}"></script>
    <script type="text/javascript">

        $('.select2 ').select2();

        $(function() {
            $("#assessment_reschedule_note").hide();
            var val = $(this).find(":selected").val();
            console.log(val);
            if(val == "reschedule"){
                $("#assessment_reschedule_note").show();
            } else {
                $("#assessment_reschedule_note").hide();
            }
            $("#markAssessment").change(function() {
                var val = $(this).find(":selected").val();
                console.log(val);
                if(val == "reschedule"){
                    $("#assessment_reschedule_note").show();
                } else {
                    $("#assessment_reschedule_note").hide();
                }
            });
        });

        class MyUploadAdapter {
            constructor( loader ) {
                this.loader = loader;
            }
            upload() {
                return this.loader.file
                    .then( file => new Promise( ( resolve, reject ) => {
                        this._initRequest();
                        this._initListeners( resolve, reject, file );
                        this._sendRequest( file );
                    } ) );
            }
            abort() {
                if ( this.xhr ) {
                    this.xhr.abort();
                }
            }

            _initRequest() {
                const xhr = this.xhr = new XMLHttpRequest();
                xhr.open( 'POST', '{{route('admin.assessments.exam-settings.ck-image-upload')}}', true );
                xhr.setRequestHeader('x-csrf-token', '{{ csrf_token() }}')
                xhr.responseType = 'json';
            }

            _initListeners( resolve, reject, file ) {
                const xhr = this.xhr;
                const loader = this.loader;
                const genericErrorText = `Couldn't upload file: ${ file.name }.`;

                xhr.addEventListener( 'error', () => reject( genericErrorText ) );
                xhr.addEventListener( 'abort', () => reject() );
                xhr.addEventListener( 'load', () => {
                    const response = xhr.response;
                    if ( !response || response.error ) {
                        return reject( response && response.error ? response.error.message : genericErrorText );
                    }
                    resolve( {
                        default: response.url
                    });
                });
                
                if ( xhr.upload ) {
                    xhr.upload.addEventListener( 'progress', evt => {
                        if ( evt.lengthComputable ) {
                            loader.uploadTotal = evt.total;
                            loader.uploaded = evt.loaded;
                        }
                    });
                }
            }

            _sendRequest( file ) {
                const data = new FormData();
                data.append( 'upload', file );
                this.xhr.send( data );
            }
        }

        function SimpleUploadAdapterPlugin( editor ) {
            editor.plugins.get( 'FileRepository' ).createUploadAdapter = ( loader ) => {
                // Configure the URL to the upload script in your back-end here!
                return new MyUploadAdapter( loader );
            };
        }

            document.querySelectorAll('.template_text').forEach(function(templateText){
                ClassicEditor
                    .create(templateText,  {
                        extraPlugins: [ SimpleUploadAdapterPlugin ],
                    }).then(editor => {
                        editor.enableReadOnlyMode("editor");
                    }).catch(error => { console.error(error)})
            })


        function collapseToggle(data_id){
            $('.que_'+data_id).find('i').toggleClass('fas fa-minus fas fa-plus');
        }

        var checkboxItem = ":checkbox";
        $('.multiCheck').click(function() {
            if (this.checked) {
                $(checkboxItem).each(function() {
                    this.checked = true;
                });
                } else {
                $(checkboxItem).each(function() {
                    this.checked = false;
                });
            }
        });

        var review_arr = [];
        $("#save_assessment").on('click', function(){
            var checkValues = $('input[name=submission_ids]:checked').map(function(){
                var submissionId = $(this).val();
                var radioGroup = $('input[name="is_passed[' + submissionId + ']"]');
                var radioValue = radioGroup.filter(':checked').val();
                
                review_arr.push({'id':submissionId, 'value':radioValue})
                return submissionId;
            }).get();

            var student_enr_id = $('input[name=student_enr_id]').val();
            var pass_marking = review_arr;
            var submitted_assessment_id = $('input[name=submitted_assessment_id]').val(); 
            var _token = $('input[name=_token]').val();
            var assessmentStatus = $("select#markAssessment option").filter(":selected").val()
            var assessmentRemark = $('textarea[name=assessment_reschedule_note]').val();
            var assessmentRecovery = $('textarea[name=assessment_recovery]').val();

            $.ajax({
                headers: { 'X-CSRF-TOKEN': _token },
                url: '{{route('admin.assessments.exam-settings.exam.reviews')}}',
                type: "POST",
                dataType: "JSON",
                data: {
                    assessment_ids: checkValues,
                    student_enr_id: student_enr_id,
                    submitted_assessment_id: submitted_assessment_id,
                    pass_marking:pass_marking,
                    is_passed: assessmentStatus,
                    assessment_reschedule_note: assessmentRemark,
                    assessment_recovery: assessmentRecovery,
                },
                success: function(res) {
                    showToast(res.message, 1);
                    if(res.success){
                        window.location.replace('{{ route('admin.assessments.examdashboard.view_trainees',  $studentData->course_id) }}');
                    }
                }
            }); // end ajax
        });
        
        $("body").on('click', '.multiReview', function(){
            if({{count($allStudentQuestionAns)}} == $('.multiReview:checked').length){
                $('.multiCheck').prop('checked', true);
            } else {
                $('.multiCheck').prop('checked', false);
            }
        })

        if({{count($allStudentQuestionAns)}} == $('.multiReview:checked').length){
            $('.multiCheck').prop('checked', true);
        } else {
            $('.multiCheck').prop('checked', false);
        }

        $("input[id='passed_all']").click(function() {
            if ($(this).is(':checked')) {
                $("input[id^='is_passed_']").prop('checked', true);
                $("input[id^='is_failed_']").prop('checked', false);
            }
        });

        $("input[id='failed_all']").click(function() {
            if ($(this).is(':checked')) {
                $("input[id^='is_failed_']").prop('checked', true);
                $("input[id^='is_passed_']").prop('checked', false);
            }
        });


        if({{count($allStudentQuestionAns)}} == $("input[id^='is_passed_']:checked").length) {
            $("input[id='passed_all").prop('checked', true);
        } else {
            $("input[id='passed_all").prop('checked', false);
        }

        if({{count($allStudentQuestionAns)}} == $("input[id^='is_failed_']:checked").length) {
            $("input[id='failed_all").prop('checked', true);
        } else {
            $("input[id='failed_all").prop('checked', false);
        }

        $("input[id^='is_passed_'], input[id^='is_failed_']").change(function() {
            const totalPassed = $("input[id^='is_passed_']").length;
            const selectedPassed = $("input[id^='is_passed_']:checked").length;

            const totalFailed = $("input[id^='is_failed_']").length;
            const selectedFailed = $("input[id^='is_failed_']:checked").length;
            console.log($("input[id='passed_all']"));
            console.log($("input[id='failed_all']"));
            $("input[id='passed_all']").prop('checked', totalPassed == selectedPassed);
            $("input[id='failed_all']").prop('checked', totalFailed == selectedFailed);
        });
    </script>
@endpush