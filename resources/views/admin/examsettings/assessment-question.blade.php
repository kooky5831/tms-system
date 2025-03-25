@extends('admin.layouts.master')
@section('title', 'Add Questions')
@push('css')
<!-- DataTables -->
<link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/fancybox/jquery.fancybox.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    /* .question-wrapper .list-group .collapse-module button.btn-module { position: absolute; right: 20px; top: 12px; z-index: 1; } */
    .ck-editor__editable_inline {
        min-height: 300px;
    }
    .question-wrapper .list-group .list-group-item { padding: 0.5rem 1.25rem; }
    .thumb-image{
        float:left;width:100px;
        position:relative;
        padding:5px;
    }

    .btn.close-icon-style{
    height: 18px; 
    width: 18px;
    position: absolute;
    top: 2px; 
    right: 14px;
    font-weight: 400;
    font-size: 14px;
    text-align: center;
    background-color: red;
    color: #FFFFFF !important;
    border-radius: 100%;
    display: flex;
    align-items: center; 
    justify-content: center;
    padding: 0px;
    cursor: pointer;
}

    #question-list .list-group-item .collapse-module h5 {width: 75%; }
   #question-list .list-group-item .collapse-module .btn.btn-success, #question-list .list-group-item .collapse-module .btn.btn-danger { height: 44px; width: 40px; top: 50%;
    position: relative; transform: translateY(-50%); }

    #assessmentdiv .btn-module{ height:40px }
    #assessmentdiv .btn-delete{ height:40px }
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
                        <li class="breadcrumb-item"><a href="#"><i class="dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="#">Exam's Questions</a></li>
                        <li class="breadcrumb-item active">Add</li>
                    </ol>
                </div>
                <h4 class="page-title">Add Questions</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <a href="{{ route('admin.assessments.exam-settings.get-assessments', $getCourseMain->id) }}" class="btn btn-primary btn-info mb-2 float-lg-right">Back</a>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4>Course Main:
                        ({{$getCourseMain->courseMain[0]->name}}) : {{$allCourseRunQuestion->title}}
                    </h4>
                    <div class="col-12 question-wrapper disabled-content">
                        <form method="POST" action="{{route('admin.assessments.exam-settings.add-questions', $allCourseRunQuestion->id)}}" id="question_form" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" value="{{ $latestQuesId }}" id="latest_que_id">
                            @php $count = 0 @endphp
                            <div id="assessmentdiv">
                                <div class="repeater-custom-show-hide">
                                    <div data-repeater-list="assessment">
                                        @if($allCourseRunQuestion->questions->isEmpty())
                                        <div data-repeater-item="">
                                            <ul class="list-group">
                                                <li class="list-group-item mb-3 col-12">
                                                    <div class="collapse-module d-flex justify-content-between">
                                                        <h5 class="mb-3">Question {{$count+1}} : </h5>
                                                        <div class="d-flex ">
                                                            <button class="btn btn-success btn-dark btn-module que_{{$count+1}}" type="button" data-toggle="collapse" data-target="#que_{{$count+1}}" aria-expanded="false" aria-controls="collapseExample" onclick="collapseToggle({{$count+1}})"><i class="fas fa-plus"></i></button>
                                                            
                                                            <span data-repeater-delete="" class="btn btn-danger btn-delete ml-3">
                                                                <span class="far fa-trash-alt"></span>
                                                            </span>
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="module-form collapse" id="que_{{$count+1}}">   
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label for="description">Question <span class="text-danger">*</span></label>
                                                                    <input type="text" id="question" class="form-control question" name="assessment[0][question]" value="">
                                                                </div>
                                                            @error('assessment[0][question]')
                                                                <label class="form-text text-danger validation-invalid-label">{{ $message }}</label>
                                                            @enderror
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label for="question">Question Image</label>
                                                                    <input type="file" class="form-control imagecount" id="upload_file_{{$count+1}}" name="assessment[0][upload_file]" onchange="preview_image({{$count+1}});" multiple/>
                                                                </div>
                                                                <input class="settedCount" type="hidden" id="settedCount_0" value="0" data-settedcount="0">
                                                                <div class="image_preview row" id="image_preview_0" style="display: inline-block;">   
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <label for="template_text">Answer format<span class="text-danger">*</span> 
                                                                    <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Please refer the dynamic variable from below list."></i>
                                                                </label>
                                                                <textarea id="template_text" name="assessment[0][template_text]" class="form-control h-auto template_text ckeditor" rows="8"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    @else
                                    
                                    
                                        @foreach ($allCourseRunQuestion->questions as $key => $question)
                                        @php  $count++ @endphp
                                        <div data-repeater-item="">
                                            {{-- <ul class="list-group" id="question-list"> --}}
                                                <li class="list-group-item mb-3 col-12">
                                                    <div class="collapse-module d-flex justify-content-between">
                                                        <h5 class="mb-3">Question {{$key+1}} : {{$question->question}}</h5>
                                                        <div class="d-flex">
                                                            <button class="btn btn-success btn-dark btn-module que_{{$question->id}}" type="button" data-toggle="collapse" data-target="#que_{{$question->id}}" aria-expanded="false" aria-controls="collapseExample" onclick="collapseToggle({{$question->id}})"><i class="fas fa-plus"></i></button>
                                                            
                                                            <span data-repeater-delete="" class="btn btn-danger btn-delete ml-3">
                                                                <span class="far fa-trash-alt"></span>
                                                            </span>

                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="assessment[{{$key+1}}][question_id]" value="{{$question->id}}">
                                                    <div class="module-form collapse" id="que_{{$question->id}}">   
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label for="question">Question <span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control question-text" name="assessment[{{$key+1}}][question]" value="{{$question->question}}" @readonly(isset($question->examQuestionsSubmission[0]) && $question->examQuestionsSubmission[0]->question_id == $question->id)>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label for="question">Question Image</label>
                                                                    <input type="file" class="form-control que_image" id="upload_file_{{$key+1}}" name="assessment[{{$key+1}}][upload_file]" onchange="preview_image({{$key+1}})" @disabled(isset($question->examQuestionsSubmission[0]) && $question->examQuestionsSubmission[0]->question_id == $question->id) multiple/>
                                                                </div>
                                                                <input type="hidden" class="settedCount" id="settedCount_{{$key+1}}" value="{{ $question->questionImages->count() }}">
                                                                <div class="image_preview row" id="image_preview_{{$key+1}}" style="display: inline-block;">
                                                                    @foreach($question->questionImages as $images)
                                                                        @if($question->id == $images->question_id)
                                                                            <div class="image-field-{{$images->id}} position-relative float-left col-lg-4" data-img="setted">
                                                                                <a href="{{asset('storage/images/question-images/'.$images->question_image)}}" data-fancybox="group">
                                                                                    <span class="btn text-danger close-icon-style" style="" onclick="removeImage({{$images->id}})">x</span>
                                                                                    <img class='b'  src='{{asset('storage/images/question-images/'.$images->question_image)}}' style='width:100%; float: left;'>
                                                                                </a>
                                                                            </div>
                                                                        @endif
                                                                    @endforeach
                                                                </div>


                                                            </div>
                                                        </div>
                                                        <div class="image_preview" id="image_preview_{{$key+1}}" style="display: inline-block;"></div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <label for="template_text_{{$key+1}}">Answer format<span class="text-danger">*</span> 
                                                                    <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Please refer the dynamic variable from below list."></i>
                                                                </label>
                                                                <textarea id="template_text" name="assessment[{{$key+1}}][template_text]" class="form-control h-auto template_text ckeditor @if(isset($question->examQuestionsSubmission[0]) && $question->examQuestionsSubmission[0]->question_id == $question->id)is-disable @endif" rows="8" @if(isset($question->examQuestionsSubmission[0]) && $question->examQuestionsSubmission[0]->question_id == $question->id) data-status = "disable" @endif >{{$question->answer_format}}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            {{-- </ul> --}}
                                        </div>
                                        @endforeach
                                        @endif

                                    </div><!--end repet-list-->

                                    <div class="form-group row mb-0 text-center">
                                        <div class="col-sm-12">
                                            <span data-repeater-create="" class="btn btn-secondary btn-md reapet-add">
                                                <span class="white-add-ico"></span> Add Question
                                            </span>
                                        </div><!--end col-->
                                    </div><!--end row-->
                                </div> <!--end repeter-->
                            </div>
                            <button type="submit" class="btn btn-primary mar-r-10">Submit</button>
                        </form>
                    </div>
                    <input type="hidden" id="counts" value="{{$count}}">
                </div>
                <div class="card-footer m-0 clearfix">
                </div>       
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
{{-- <script src="https://cdn.jsdelivr.net/npm/@fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script> --}}
<script src="{{ asset('assets/plugins/fancybox/jquery.fancybox.min.js') }}" ></script>
<script src="{{ asset('assets/plugins/repeater/jquery.repeater.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/ckeditor5/ckeditor.js') }}"></script>


<script type="text/javascript">
    $(document).ready(function() {
        
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

        
        if($("#counts").val() > 0){
            document.querySelectorAll('.template_text').forEach(function(templateText){
                if (templateText.classList.contains('is-disable')) {
                    ClassicEditor
                        .create(templateText)
                        .then(editor => {
                            editor.enableReadOnlyMode('feature-id') // make the editor read-only right after initialization
                        }) .catch( error => { 
                            console.error( error ); 
                        });
                } else {
                    ClassicEditor
                        .create(templateText,  {
                            extraPlugins: [ SimpleUploadAdapterPlugin],
                        }).catch(error => { console.error(error)})
                        // console.log(templateText)
                }
            })
        }

        // Initialize the Repeater on the ul element
        $('.repeater-custom-show-hide').repeater({
            @if( $allCourseRunQuestion->questions->isEmpty() )
            initEmpty: true,
            @endif
            isFirstItemUndeletable: true,
            show: function () {
                $(this).slideDown();
                
                $(this).find('.question-text').each(function() {
                    $(this).removeAttr("readonly");
                })
                
                $(this).find('.que_image').each(function(){
                    $(this).removeAttr("readonly");
                    $(this).removeAttr("disabled");
                })
                
                if($("#counts").val()){

                    var idNum = Number($("#counts").val())+1;
                    $('#counts').val(Number($("#counts").val())+1);

                    var latestQueId = (Number($('#latest_que_id').val())) + (Number($("#counts").val()));
                    $($(this).find('[id]')[0]).attr('id', 'que_'+latestQueId)

                    var $collapseButton = $(this).find('.btn-success');
                    $collapseButton.attr('data-target', '#que_' + latestQueId);
                    $collapseButton.attr('onclick', 'collapseToggle(' +latestQueId+ ')');
                    
                    var $imageCount = $(this).find('.imagecount');
                    $imageCount.attr('id', 'upload_file_'+idNum);
                    $imageCount.attr('onchange', 'preview_image(' +idNum+ ')');

                    var $imgInput = $(this).find('.que_image');
                    $imgInput.attr('id', 'upload_file_'+idNum);
                    $imgInput.attr('onchange', 'preview_image(' +idNum+ ')');

                    var $imgPreview = $(this).find('.image_preview');
                    $imgPreview.attr('id', 'image_preview_'+idNum);

                    $('#image_preview_'+idNum).html("");

                    var $settedCount = $(this).find('.settedCount');
                    $settedCount.attr('id', 'settedCount_'+idNum);
                    $settedCount.attr('data-settedcount', idNum);
                    $settedCount.val(0);

                    $('#settedCount_'+idNum).html("");
                    
                    var $h5 = $(this).find('h5');
                    $h5.text('Question ' + idNum + ' :');
                    
                    var $answer_format  = $(this).find('.ckeditor');
                    $answer_format.attr('id', 'template_text_'+idNum);

                    $(this).find('.ckeditor').each(function() {
                        //CKEDITOR.replace($(this).attr('id'));
                        var id = "#"+$(this).attr('id');
                        ClassicEditor.create(document.querySelector(id), {
                            extraPlugins: [ SimpleUploadAdapterPlugin],
                        }).catch(error => {
                            console.error(error)
                        })
                    })

                }
            },
            hide: function (remove) {
                var idNum = Number($("#counts").val());
                $('#counts').val((Number(idNum)-1));
                if (confirm('Are you sure you want to remove this item?')) {
                    $(this).slideUp(remove);
                }
            },
        });  
    });

    function collapseToggle(data_id){
        $('.que_'+data_id).find('i').toggleClass('fas fa-plus fas fa-minus');
    }

    var imgId = null;
    function preview_image(qustionImageId) 
    {
        imgId = qustionImageId;

        var total_file = document.getElementById("upload_file_" + qustionImageId +"").files.length;
        var fileInput = document.getElementById("upload_file_" + imgId +"")


        var maxImages = 10;
            var settedImageCount = $('#settedCount_'+ qustionImageId).val();
            var remainingUnsetImages = maxImages - parseInt(settedImageCount);
      
            $('#image_preview_' + qustionImageId + ' [data-img="unset"]').remove();
        
        for(var i=0;i<total_file;i++){
            if( remainingUnsetImages > 0) {
            $('#image_preview_' + qustionImageId +'').append('<div class="image-field-'+i+' position-relative float-left col-lg-4" id="image-field-'+i+'" data-img="unset">'+
                '<a href="'+URL.createObjectURL(event.target.files[i])+'" data-fancybox="group">'+
                    '<span class="btn text-danger close-icon-style" style="" onclick="cancelImage('+i+')">x</span>'+
                    "<img class='b'  src='"+URL.createObjectURL(event.target.files[i])+"' style='width:100%; float: left;'>"+
                '</a>'+
            '</div>');

            remainingUnsetImages--;
            } else {
                showToast("You are only allowed to upload a maximum of 10 files", 0);
                $("#upload_file_" + imgId +"").val('');
                $('#image_preview_' + qustionImageId + ' [data-img="unset"]').remove();
                return false;
            }
        }
        var oldImage = null;
        var newImage = "";
        var count = 0; 
        var unsetCount = 0;
        var data = ""
        $('#image_preview_' + qustionImageId).children('div').each(function(index, element){
            count++;
            var dataImgValue = $(element).attr('data-img');
            if(dataImgValue == "setted"){
                newImage = "<div class='image-field-"+ count +" position-relative float-left col-lg-4' data-img='setted' onclick='removeCurrunt("+count+")'>"+$(element).html()+"</div>";
            } else {
                    unsetCount++;
                    data = "<div class='image-field-"+ count +" position-relative float-left col-lg-4' data-img='unset'onclick='removeCurrunt("+count+")'>"+$(element).html()+"</div>";
            }
        })
    }

    function removeImage(imageId){
        $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            url: '{{ route('admin.assessments.exam-settings.delete-image') }}',
            type: "POST",
            dataType: "JSON",
            data: {
                id: imageId
            },
            success: function(res) {
                // showToast(res.msg, 1);
                $('.image-field-'+imageId).html("");
            }
        })
    }

    function cancelImage(imageId){
        $('#image-field-'+imageId).html("");

        var fileInput = document.getElementById("upload_file_" + imgId +"")

        if (fileInput && fileInput.files instanceof FileList) {
            var updatedFiles = Array.from(fileInput.files).filter(function (_, index) {
                return index !== imageId;
            });

            var newFileList = new DataTransfer();
            updatedFiles.forEach(function (file) {
                newFileList.items.add(file);
            });

            // Set the updated files back to the input field
            fileInput.files = newFileList.files;
        }
    }

    function removeCurrunt(curruntId){
        $('.image-field-'+curruntId).html("");
    }

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
</script>
@endpush