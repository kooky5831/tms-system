@extends('assessments::student.layouts.master')
@section('title', 'Student Enrolment List')
@push('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/fancybox/jquery.fancybox.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/dropzone/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
    
<style>
    .disabled-content{ pointer-events: none; opacity: 0.4; }
    .prev, .next { padding: 5px 8px; font-size: 14px; color:#fff !important; cursor: pointer; }
    .lbl_answer{ color: #008000; }
    .countdown-label { font-weight: 600; color: #65584c; text-align: center; text-transform: uppercase; display: inline-block; letter-spacing: 2px; margin-top: 10px }
    #countdown{ box-shadow: 0 1px 2px 0 rgba(1, 1, 1, 0.4); width: 240px; height: 96px; text-align: center; background: #f1f1f1;border-radius: 5px; margin: auto; }
    #countdown #tiles{ color: #fff; position: relative; z-index: 1; text-shadow: 1px 1px 0px #ccc; display: inline-block; text-align: center; padding: 10px; border-radius: 5px 5px 0 0; font-size: 24px; font-weight: thin; display: block; }
    .color-full { background: #4b80d8; }
    .color-half { background: #ebc85d; }
    .color-empty { background: #e5554e; }
    #countdown #tiles > span{ width: 70px; max-width: 70px; padding: 18px 0; position: relative; }
    .question-wrapper .list-group .collapse-module { width: 97%; }
    .question-wrapper .list-group .collapse-module button.btn-module { position: absolute; right: 10px; top: 12px; z-index: 1; }
    .question-wrapper .list-group .list-group-item { padding: 0.5rem 1.25rem; }
    #finish, #save_all_ans { width: 100px; padding: 10px;font-size: 18px; }
    .ck-editor__editable_inline { min-height: 300px; }
    .no-data-found { margin: 0 auto; }
    .attachment-upload{margin-top: 35px;}
    .snippet-autosave-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: var(--ck-color-toolbar-background);
        border: 1px solid var(--ck-color-toolbar-border);
        padding: 10px;
        border-radius: var(--ck-border-radius);
        /* margin-top: -1.5em; */
        margin-bottom: 1.5em;
        border-top: 0;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }
    .snippet-autosave-status_spinner {
        display: flex;
        align-items: center;
        position: relative;
    }

    .snippet-autosave-status_spinner-label {
        position: relative;
    }

    .snippet-autosave-status_spinner-label::after {
        content: 'Saved!';
        color: green;
        display: inline-block;
        margin-right: var(--ck-spacing-medium);
    }

    /* During "Saving" display spinner and change content of label. */
    .snippet-autosave-status.busy .snippet-autosave-status_spinner-label::after {
        content: 'Saving...';
        color: red;
    }

    .snippet-autosave-status.busy .snippet-autosave-status_spinner-loader {
        display: block;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border-top: 3px solid hsl(0, 0%, 70%);
        border-right: 2px solid transparent;
        animation: autosave-status-spinner 1s linear infinite;
    }

    .snippet-autosave-status, .snippet-autosave-server {
        display: flex;
        align-items: center;
    }

    .snippet-autosave-server_label, .snippet-autosave-status_label {
        font-weight: bold;
        margin-right: var(--ck-spacing-medium);
    }

    .snippet-autosave + .ck.ck-editor .ck-editor__editable {
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 0;
    }

    .snippet-autosave-lag {
        padding: 4px;
    }

    .snippet-autosave-console {
        max-height: 300px;
        overflow: auto;
        white-space: normal;
        background: #2b2c26;
        transition: background-color 500ms;
    }

    .snippet-autosave-console.updated {
        background: green;
    }

    @keyframes autosave-status-spinner {
        to {
            transform: rotate( 360deg );
        }
    }

    .saving-loder{
        font-size: 18px;
        padding: 10px;
        pointer-events: none;
    }

    .exam_note p{ color: red;font-size: 16px; }
    
</style>
<script type="text/javascript">
    function preventBack() { window.history.forward(); }
    setTimeout("preventBack()", 0);
    window.onunload = function () { null };
</script>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row assessment" id="assessment">
            <div class="exam_note">
                <p> Note: Please do not close the window before answer status show saved message under answer box.</p>
            </div>
            @if($allQuestion->questions->isNotEmpty())
            
            <div class="w-100 d-flex align-items-center justify-content-between mb-4">
                <div class="float-left">
                    <button class="btn btn-primary start-now" id="start-now">
                        Start Now!
                    </button>
                </div>

                <div class="justify-content-end">
                    <div id="countdown">
                        <div id='tiles' class="color-full">{{ Carbon\Carbon::parse($assessmentExam->assessment_duration)->format('H:i:s') }} </div>
                        <div id ="left" class="countdown-label">Time Remaining</div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="assessment_id" value="{{$assessmentId}}" class="assessment_id" id="assessment_id">
            <input type="hidden" name="student_enr_id" value="{{$student_id}}" class="student_id" id="student_id">
            <input type="hidden" name="courseId" value="{{$courseId}}" class="courseId" id="course_id">

            <div class="col-12 question-wrapper disabled-content">
                @php $count = 0 @endphp
                
                @if(count($submitedQA) > 0)

                    <ul class="list-group">
                        <h3>{{$allQuestion->title}}</h3>
                        @foreach ($submitedQA as $question)
                        @php $count++ @endphp
                        <li class="list-group-item mb-3 mt-2">
                            <div class="collapse-module">
                                <h4 class="mb-3 mr-3">Question {{$count}}: {{$question->examQuestion->question}}</h4>
                                <button class="btn btn-success btn-dark btn-module que_{{$question->examQuestion->id}}" type="button" data-toggle="collapse" data-target="#que_{{$question->examQuestion->id}}" aria-expanded="false" aria-controls="collapseExample" onclick="collapseToggle({{$question->examQuestion->id}})"><i class="fas fa-plus"></i></button>
                            </div>
                            <div class="module-form collapse" id="que_{{$question->examQuestion->id}}">
                                <form class="exam-forms frm_index_{{$question->examQuestion->id}}" enctype="multipart/form-data">

                                        <input type="hidden" name="question_id" value="{{$question->examQuestion->id}}" class="question_id" id="question_{{$question->examQuestion->id}}">
                                        
                                        {{-- <div class="row"> --}}
                                            @if($question->examQuestion->questionImages->count() > 0)
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="question">Question Image:</label>
                                                    </div>
                                                    <div class="image_preview row" style="display: inline-block;">
                                                        @foreach($question->examQuestion->questionImages as $images)
                                                            <a href="{{asset('storage/images/question-images/'.$images->question_image)}}" data-fancybox="group">
                                                                <div class="image-field-{{$images->id}} position-relative float-left col-lg-4">
                                                                    <img class='b'  src='{{asset('storage/images/question-images/'.$images->question_image)}}' style='width:100%; float: left;'>
                                                                </div>
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        {{-- </div> --}}

                                        <div class="row mt-3">
                                            <div class="col-12">
                                                @php
                                                    $answer_value = "";
                                                    if($question->question_id == $question->examQuestion->id){
                                                        $answer_value = $question->submitted_answer;
                                                    }
                                                @endphp
                                                <label for="template_text_{{$question->examQuestion->id}}" class="lbl_answer">Answer <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Please give your answer below."></i></label>
                                                <textarea id="template_text_{{$question->examQuestion->id}}"  name="template_text" class="form-control h-auto template_text" rows="8" required> 
                                                    @if($answer_value != null)
                                                        {{$answer_value}}
                                                    @else
                                                        {{$question->examQuestion->answer_format}}
                                                    @endif
                                                </textarea>
                                                <div class="ck ck-content snippet-autosave-header">
                                                    <div id="snippet-autosave-status_{{$question->examQuestion->id}}" class="snippet-autosave-status">
                                                        <div class="snippet-autosave-status_label">Status:</div>
                                                        <div class="snippet-autosave-status_spinner">
                                                            <span class="snippet-autosave-status_spinner-label"></span>
                                                            <span class="snippet-autosave-status_spinner-loader"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <div class="dropzone" id="my-dropzone-{{$question->examQuestion->id}}">
                                                </div>
                                            </div>
                                        </div>
                                </form>
                            </div>
                        </li>
                        @endforeach
                    </ul>

                @else

                    <ul class="list-group">
                        <h3>{{$allQuestion->title}}</h3>
                        @foreach ($allQuestion->questions as $question)
                        @php $count++ @endphp
                        <li class="list-group-item mb-3 mt-2">
                            <div class="collapse-module">
                                <h4 class="mb-3 mr-3">Question {{$count}}: {{$question->question}}</h4>
                                <button class="btn btn-success btn-dark btn-module que_{{$question->id}}" type="button" data-toggle="collapse" data-target="#que_{{$question->id}}" aria-expanded="false" aria-controls="collapseExample" onclick="collapseToggle({{$question->id}})"><i class="fas fa-plus"></i></button>
                            </div>
                            <div class="module-form collapse" id="que_{{$question->id}}">
                                <form class="exam-forms frm_index_{{$question->id}}" enctype="multipart/form-data">

                                        <input type="hidden" name="question_id" value="{{$question->id}}" class="question_id" id="question_{{$question->id}}">
                                        
                                        {{-- <div class="row"> --}}
                                            @if($question->questionImages->count() > 0)
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="question">Question Image:</label>
                                                    </div>
                                                    <div class="image_preview row" style="display: inline-block;">
                                                        @foreach($question->questionImages as $images)
                                                            <a href="{{asset('storage/images/question-images/'.$images->question_image)}}" data-fancybox="group">
                                                                <div class="image-field-{{$images->id}} position-relative float-left col-lg-4">
                                                                    <img class='b'  src='{{asset('storage/images/question-images/'.$images->question_image)}}' style='width:100%; float: left;'>
                                                                </div>
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        {{-- </div> --}}

                                        <div class="row mt-3">
                                            <div class="col-12">
                                                @php
                                                    $answer_value = "";
                                                    if($question->answer_que_id == $question->id){
                                                        $answer_value = $question->submitted_answer;
                                                    }
                                                @endphp
                                                <label for="template_text_{{$question->id}}" class="lbl_answer">Answer <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Please give your answer below."></i></label>
                                                <textarea id="template_text_{{$question->id}}"  name="template_text" class="form-control h-auto template_text" rows="8" required> 
                                                    @if($answer_value == "" || empty($answer_value))
                                                        {{$question->answer_format}}
                                                    @else
                                                        {{$answer_value}}
                                                    @endif
                                                </textarea>
                                                <div class="ck ck-content snippet-autosave-header">
                                                    <div id="snippet-autosave-status_{{$question->id}}" class="snippet-autosave-status">
                                                        <div class="snippet-autosave-status_label">Status:</div>
                                                        <div class="snippet-autosave-status_spinner">
                                                            <span class="snippet-autosave-status_spinner-label"></span>
                                                            <span class="snippet-autosave-status_spinner-loader"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <div class="dropzone" id="my-dropzone-{{$question->id}}">
                                                </div>
                                            </div>
                                        </div>
                                </form>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                
                @endif
                
                <div class="row">
                    <div class="col-md-12 mt-4">
                        <a id="finish" class="next btn btn-success btn-sm mr-2" data-assessmentid="{{ $assessmentId }}" data-studentid="{{ $student_id }}" data-coursesid="{{$courseId}}">Finish</a>
                        <a id="save_all_ans" class="next btn btn-success btn-sm mr-2" data-assessmentid="{{ $assessmentId }}" data-studentid="{{ $student_id }}" data-coursesid="{{$courseId}}">Save All</a>
                        
                        <button class="next btn btn-success btn-sm mr-2 saving-loder" id="loading_btn" disabled style="display: none"> <i class="fas fa-sync fa-spin"></i> Saving </button>
                    </div>
                </div>
            </div>
            @else
                <h3 class="no-data-found">No Questions Added. Please contact Course Trainer.</h3>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    {{-- <script src="https://cdn.ckeditor.com/4.11.2/full/ckeditor.js"></script> --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/@fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script> --}}
    {{-- <script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script> --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js" type="text/javascript"></script> --}}
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/dropify/js/dropify.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/jquery.form-upload.init.js') }}"></script>
    <script src="{{ asset('assets/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
    {{-- <script src="{{ asset('assets/plugins/ckeditor5/ckeditor.js') }}" ></script> --}}
    <script src="{{asset('assets/vendor/ckeditor5/build/ckeditor.js')}}"></script>
    <script src="{{ asset('assets/plugins/fancybox/jquery.fancybox.min.js') }}" ></script>
    <script src="{{ asset('assets/plugins/dropzone/dropzone.min.js') }}" ></script>

    <script type="text/javascript">
        
        //Dropzone Configuration
        Dropzone.autoDiscover = false;

        var assessmentId = '{{$assessmentId}}';
        var curruntStudentId = '{{$student_id}}';
        var courseId = '{{$courseId}}';
        var lastTime = "";
        var examId = '{{$examId}}';
        var editors = {};

        $(document).ready(function(){

            document.querySelectorAll('.exam-forms').forEach(function(check){
                
                var questionID = $("#"+$(check).find('.question_id').attr('id')).val();
                var formData = new FormData();
                var dropZoneID = $(check).find('.dropzone').attr('id');

                $('#'+dropZoneID).dropzone({
                    addRemoveLinks: true,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    url: '{{ route('student.assessmet.exam.attachment') }}',
                    type: "POST",
                    sending: function(file, xhr, formData) {
                        // console.log('file', file)
                        formData.append('student_enr_id', curruntStudentId);
                        formData.append('assessment_id', assessmentId);
                        formData.append('question_id', questionID);
                        formData.append('submission_attchment', file);
                    },
                    init: function() {
                        var myDropzone = this;
                        $.ajax({
                            url: '{{ route('student.assessmet.exam.attachment.get', $student_id) }}',
                            type: 'GET',
                            dataType: 'json',
                            success: function(res){
                                $.each(res.data, function (key, value) {                               
                                    if(questionID == value.question_id){
                                        var file = {name: value.submission_attchment, size: value.attachment_size};
                                        myDropzone.options.addedfile.call(myDropzone, file);
                                        myDropzone.emit("complete", file);
                                    }
                                });
                            }
                        });
                    },
                    success:function(res){
                    },
                    removedfile: function(file) {
                        $.ajax({
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            url: '{{ route('student.assessmet.exam.attachment.delete') }}',
                            dataType: 'html',
                            type: "POST",
                            data:{
                                filename:file.name,
                                questionId:questionID,
                                examId:examId,
                                assessmentID:assessmentId,
                                curruntStudentId:curruntStudentId
                            },
                        });
                        var fileRef;
                        return (fileRef = file.previewElement) != null ?
                        fileRef.parentNode.removeChild(file.previewElement) : void 0;
                    },
                });
            })

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
                ClassicEditor.create(templateText, {
                    toolbar: ['undo','redo' ,'heading', '|','bold', 'italic', '|' ,'link','imageUpload' ,'insertTable' ,'blockQuote', 'MediaEmbed', '|','bulletedList' ,'numberedList', 'Indent'],
                    table: {
                        contentToolbar: [ 'tableColumn', 'tableRow', 'mergeTableCells',]
                    },
                    image: {
                        toolbar: [ 'imageStyle:side', '|', 'imageTextAlternative' ],
                    },
                    autosave: {
                        waitingTime: 2000,
                        save( editor) {
                            var editorId = templateText.id;
                            return saveData(editorId, editor.getData());
                        }
                    },
                    extraPlugins: [ SimpleUploadAdapterPlugin,],
                }).then( editor => {
                    editors[templateText.id] = editor;
                    displayStatus( templateText, editor );
                    handleBeforeunload( editor );
                })
                .catch(error => {
                    console.error(error)
                });
            })
            
            function saveData(editorId, ediotrData) {
                return new Promise(resolve => {
                    setTimeout(() => {
                        var questionId = editorId.split("template_text_");
                        
                        var $data = { 
                            assessment_id : assessmentId, 
                            student_enrol_id : curruntStudentId,
                            question_id: questionId[1],
                            submitted_student_answer : ediotrData,
                        }

                        $.ajax({
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            url: '{{ route('student.assessmet.exam.auto.save') }}',
                            type: "POST",
                            data: JSON.stringify($data),
                            contentType: "application/json; charset=utf-8",
                            traditional: true,
                            success: function(res) {
                            },
                            error: function(err) {
                            }
                        })
                        resolve();
                    });
                });
            }

             // Update the "Status: Saving..." info.
             function displayStatus(templateText, editor) {
                // console.log('templateText', templateText.id);
                var questionId = templateText.id.split("template_text_");
                 const pendingActions = editor.plugins.get("PendingActions");
                 const statusIndicator = document.querySelector('#snippet-autosave-status_'+questionId[1]);
                

                pendingActions.on('change:hasAny', (evt, propertyName, newValue) => {
                    if (newValue) {
                        statusIndicator.classList.add('busy');
                    } else {
                        statusIndicator.classList.remove('busy');
                    }
                });
            }

            function handleBeforeunload( editor ) {
                const pendingActions = editor.plugins.get( 'PendingActions' );

                window.addEventListener( 'beforeunload', evt => {
                    if ( pendingActions.hasAny ) {
                        evt.preventDefault();
                    }
                } );
            }

            $("#finish").click(function(){
                var total_question_count = 0;
                var total_answered_count = 0;
                
                var currentForm = $('.exam-forms');
                total_question_count = currentForm.length;

                currentForm.each(function() {
                    var editorID = $(this).find('.template_text').attr('id');
                    var editorInstance      = editors[editorID];
                    var curruntEditorData   = editorInstance.getData()
                    if (curruntEditorData.length != 0) {
                        total_answered_count++;
                    }
                });

                if(lastTime != "00:00:00"){
                    if (total_question_count == total_answered_count) {
                        swal({
                                title: 'Are you sure?',
                                text: "You want to submit this exam?",
                                type: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes, Submit it!'
                        }).then(function(result) {
                            if(result.value){
                                var data = {courseid:courseId, student_enr_id:curruntStudentId}
                                $.ajax({
                                    headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                    url: '{{ route('student.assessment.feedback.getdata') }}',
                                    type: "POST",
                                    data: data,
                                    dataType: "json",
                                    success: function(res) {
                                        if(res.status == 'success') {
                                            const startDateTime   = new Date(res.data.started_at).toLocaleDateString();
                                            const currentDateTime = new Date().toLocaleDateString();

                                            if(startDateTime == currentDateTime && res.data.assessment_id == assessmentId){
                                                Swal.fire({
                                                    title: 'Submitted!',
                                                    text: 'Your assessment has been submitted and will be reviewed. Please click on the button below to leave us a testimonial and complete a quick survey. Thank you!',
                                                    type: 'success',
                                                    showConfirmButton: true,
                                                    confirmButtonText: 'Continue',
                                                }).then(function(result) {
                                                    $('<a href="https://forms.gle/YdrsyZdj7gjNSKgY6" target="_blank">External Link</a>')[0].click();
                                                    var currentForm = $('.exam-forms');
                                                    // fn_ajax_call(currentForm)
                                                    finishedExam(currentForm);
                                                });
                                            } else {
                                                swal(
                                                    'Submitted!',
                                                    'Your exam has been submitted.',
                                                    'success'
                                                ).then(function(result){
                                                    var currentForm = $('.exam-forms');
                                                    // fn_ajax_call(currentForm)
                                                    finishedExam(currentForm);
                                                });
                                            }
                                        } else {
                                            swal(
                                                'Submitted!',
                                                'Your exam has been submitted.',
                                                'success'
                                            ).then(function(result){
                                                var currentForm = $('.exam-forms');
                                                // fn_ajax_call(currentForm)
                                                finishedExam(currentForm);
                                            });
                                        }
                                    }
                                })
                            }
                        });
                    } else {
                        Swal.fire({
                            type: "error",
                            title: "Oops...",
                            text: "Please ensure that all questions are answered before submitting",
                        });
                    }
                }
            });

            $('#save_all_ans').click(function(){
                var total_question_count = 0;
                var total_answered_count = 0;

                var currentForm = $('.exam-forms');
                var formData = new FormData();

                var all_question_answer = [];
                var all_data = [];
                var i = 0;

                total_question_count = currentForm.length;

                currentForm.each(function() {
                    var editorID = $(this).find('.template_text').attr('id');
                    var questionID = $(this).find('.question_id').attr('id');
                    var answerFile = $(this).find('.answer_file').attr('id');
                    var editorInstance      = editors[editorID];
                    var curruntEditorData   = editorInstance.getData()
                    var curruntQuestionId   = $("#"+questionID).val();
                    var examID              = $("#"+examID).val();
                    var assessmentId = assessmentId;
                    var curruntStudentId =  curruntStudentId;

                    if (curruntEditorData.length != 0) {
                        total_answered_count++;
                    }

                    all_question_answer[i] = {  
                        question_id:curruntQuestionId,
                        student_enr_id:curruntStudentId,
                        exam_id:examID,
                        submitted_answer:curruntEditorData,
                        assessment_id:assessmentId
                    };
                    i++;
                });

                /* Update Status Start */
                let assessmentId = $("#finish").data('assessmentid');
                let studentEnrolId = $("#finish").data('studentid');
                let courseId = $("#finish").data('coursesid');
                var formData = new FormData();

                formData.append('assessment_id', assessmentId);
                formData.append('student_enrol_id', studentEnrolId);
                formData.append('courseId', courseId);
                formData.append('all_question_answer', JSON.stringify(all_question_answer))

                $('#save_all_ans').hide();
                $("#loading_btn").show();

                if(lastTime != "00:00:00"){
                    if (total_question_count == total_answered_count) {
                        $.ajax({
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            url: '{{ route('student.assessmet.exam.post') }}',
                            type: "POST",
                            data: formData,
                            contentType: false,
                            processData: false,
                            success: function(res) {
                                if (res.status) {
                                    showToast(res.msg, 1);
                                    $("#loading_btn").hide();
                                    $('#save_all_ans').show();
                                } else {
                                    $("#loading_btn").show();
                                    $('#save_all_ans').hide();
                                    showToast(res.msg, 0);
                                }
                            },
                            error: function(err) {
                                console.error(err);
                            }
                        });
                    } else {
                        $('#save_all_ans').show();
                        $("#loading_btn").hide();
                        Swal.fire({
                            type: "error",
                            title: "Oops...",
                            text: "Please ensure that all questions are answered before submitting",
                        });
                    }
                }
            });
        });

        function beforeunloadhandler(e){
            e.preventDefault();
            saveLastTime(lastTime);
            return e.returnValue = "Are you sure you want to exit?";
        }
       

        document.addEventListener("onpaste", (e) => {
            e.preventDefault();
        }, false);

        function saveLastTime(lastTime){
            var $data = { 
                assessment_id : assessmentId, 
                course_run_id : courseId,
                student_enrol_id : curruntStudentId,
                last_time : lastTime,
            }

            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('student.assessmet.exam.lasttime.post') }}',
                type: "POST",
                data: JSON.stringify($data),
                contentType: "application/json; charset=utf-8",
                traditional: true,
                success: function(res) {
                }
            });
        }

        function timer(seconds, countdownTimer, callback) {
            var days = Math.floor(seconds / 24 / 60 / 60);
            var hoursLeft = Math.floor((seconds) - (days * 86400));
            var hours = Math.floor(hoursLeft / 3600);
            var minutesLeft = Math.floor((hoursLeft) - (hours * 3600));
            var minutes = Math.floor(minutesLeft / 60);
            var remainingSeconds = seconds % 60;

            if (hours < 10) {
                hours = '0' + hours;
            }
            if (minutes < 10) {
                minutes = '0' + minutes;
            }
            if (remainingSeconds < 10) {
                remainingSeconds = "0" + remainingSeconds;
            }
            
            lastTime = hours + ":" +minutes + ":" + remainingSeconds;
            if(minutes % 2 == 0 && remainingSeconds == 59){
                saveLastTime(lastTime);
            }
            document.getElementById('tiles').innerHTML = hours + ":" +minutes + ":" + remainingSeconds ;
            if (seconds == 0) {
                clearInterval(countdownTimer);
                document.getElementById('tiles').innerHTML = "Time Over!";
                Swal.fire(
                    'Time Over!',
                    'Your time is over. Your exam will submit automatically',
                    'warning'
                ).then(function(result){
                    var data = {courseid:courseId, student_enr_id:curruntStudentId}
                    $.ajax({
                        headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: '{{ route('student.assessment.feedback.getdata') }}',
                        type: "POST",
                        data: data,
                        dataType: "json",
                        success: function(res) {
                            if(res.status == 'success') {
                                const startDateTime   = new Date(res.data.started_at).toLocaleDateString();
                                const currentDateTime = new Date().toLocaleDateString();
                                if(startDateTime == currentDateTime && res.data.assessment_id == assessmentId){
                                    $('<a href="https://forms.gle/YdrsyZdj7gjNSKgY6" target="_blank">External Link</a>')[0].click();
                                }
                            }
                        }
                    })
                    var currentForm = $('.exam-forms');
                    // fn_ajax_call(currentForm);
                    finishedExam(currentForm)
                });
            } else {
                seconds--;
            }
            
            //Pass seconds param back to the caller.
            callback(seconds);            
        }

        /*Countdonw timer End*/
                
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
                xhr.open( 'POST', '{{ route('student.assessment.ck-image-upload')}}', true );
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


        $('body').on('click', '#start-now', function(e) {
            // addEventListener("beforeunload", beforeunloadhandler);

            $(this).hide();

            var formData = new FormData();
            formData.append('student_enr_id', curruntStudentId);
            formData.append('assessment_id', assessmentId);
            formData.append('course_run_id', courseId);

            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('student.assessmet.exam.started.post') }}',
                type: "POST",
                data: formData,
                contentType: "application/json; charset=utf-8",
                contentType: false,
                processData:false,
                success: function(res) {
                    if( res.status ) {
                        makeFullscreen();
                        $('#tiles').text(res.time_remaining);
                        var seconds = res.inSecond
                        $('.question-wrapper').removeClass('disabled-content');
                        $('.left-sidenav').addClass('disabled-content');
                        var countdownTimer = null,
                        seconds =  res.inSecond;
                        countdownTimer = setInterval(function() {
                            timer(seconds, countdownTimer, function(_seconds){
                                seconds = _seconds;
                            })
                        }, 1000);
                    }
                    else{
                        
                        showToast(res.msg, 0);
                    }
                },
                error: function(err) {
                }
            });
        });

        function collapseToggle(data_id){
            var toggle = $('.que_'+data_id).find('i');
            
            if(toggle.hasClass('fa-minus')){
                toggle.removeClass('fa-minus').addClass('fa-plus');
            }
            else{
                toggle.removeClass('fa-plus').addClass('fa-minus');
            }
        }
        
        function finishedExam(form){

            // removeEventListener("beforeunload", beforeunloadhandler);
            saveLastTime(lastTime);
            var all_question_answer = [];
            var all_data = [];
            currentForm = form;
            var i = 0;
            currentForm.each(function() {
                var editorID = $(this).find('.template_text').attr('id');
                var questionID = $(this).find('.question_id').attr('id');
                var answerFile = $(this).find('.answer_file').attr('id');
                var editorInstance      = editors[editorID];
                var curruntEditorData   = editorInstance.getData()
                var curruntQuestionId   = $("#"+questionID).val();
                var examID              = $("#"+examID).val();
                var assessmentId = assessmentId;
                var curruntStudentId =  curruntStudentId;

                all_question_answer[i] = {  
                    question_id:curruntQuestionId,
                    student_enr_id:curruntStudentId,
                    exam_id:examID,
                    submitted_answer:curruntEditorData,
                    assessment_id:assessmentId
                };
                i++;
            });

            /* Update Status Start */
            let assessmentId = $("#finish").data('assessmentid');
            let studentEnrolId = $("#finish").data('studentid');
            let courseId = $("#finish").data('coursesid');
            var formData = new FormData();

            formData.append('assessment_id', assessmentId);
            formData.append('student_enrol_id', studentEnrolId);
            formData.append('courseId', courseId);
            formData.append('all_question_answer', JSON.stringify(all_question_answer))
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('student.assessmet.exam.finish.post') }}',
                type: "POST",
                data: formData,
                contentType: "application/json; charset=utf-8",
                contentType: false,
                processData:false,
                success: function(res) {
                    if( res.status ) {
                        showToast(res.msg, 1);
                        window.location.replace('{{ route('student.assessment.dashboard') }}');
                    }
                    else{
                        showToast(res.msg, 0);
                    }
                },
                error: function(err) {
                }
            });
            /* Update Status End */
        }
        
        function makeFullscreen(elem) {
            elem = elem || document.documentElement;
            if (!document.fullscreenElement && !document.mozFullScreenElement &&
                !document.webkitFullscreenElement && !document.msFullscreenElement) {
                if (elem.requestFullscreen) {
                elem.requestFullscreen();
                } else if (elem.msRequestFullscreen) {
                elem.msRequestFullscreen();
                } else if (elem.mozRequestFullScreen) {
                elem.mozRequestFullScreen();
                } else if (elem.webkitRequestFullscreen) {
                elem.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
                }
            }
        }
    </script>
@endpush
