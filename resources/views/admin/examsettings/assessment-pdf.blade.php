<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Assessment</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{asset('assets/css/assessment.css')}}">
    <link rel="icon" type="image/x-icon" href="{{asset('assets/images/equinetacademy-favicon.png')}}">
</head>

<body>
   
    <htmlpagefooter name="page-footer">
        <table width="100%" style="border:none;">
        <tr style="border:none;background-color:#d3d3d3">
            <td width="33%" style="border:none;"> {!! $getAssessmentName['title'] !!} </td>
            <td width="33%" align="center" style="border:none;"> Page: {PAGENO}/{nbpg} </td>
            <td width="33%" style="text-align: right;border:none;"> Equinet Academy Private Limited </td>
        </tr>
    </table>
    </htmlpagefooter>
    <main>
        
        <div class="header-details" style="display: block; width: 100%; float:left;">
            <div class="logo" style="width: 28%; float:left">
                <img src="{{ asset(Storage::url('invoice-image/invoice_logo.jpg')) }}" alt="logo" style="width: 50%; height: auto;">
            </div>
            <div class="assessment-details" style="width: 50%; float:right;margin-top: 50px;">
                @if(!empty($getStudentName['student']['name']))
                    <div class="student-name">
                        <strong>Student: {!! $getStudentName['student']['name'] !!}</strong>
                    </div>
                @endif
                @if(!empty($getCourseName['courseName']))
                    <div class="course-name">
                        <strong>Course: {!! $getCourseName['courseName'] !!}</strong>
                    </div>
                @endif
                @if(!empty($getAssessmentName['title']))
                    <div class="assessment-tilte">
                        <strong>Assessment: {!! $getAssessmentName['title'] !!}</strong>
                    </div>
                @endif
            </div>
        </div>
        <hr>
        <div class="question-wrapper">
            @php $count = 0 @endphp
            @foreach ($assessments as $qa)
                @php $count++ @endphp
                    <div class="main-question container">
                        <div class="question-assessment" style="margin-top: 25px; margin-bottom:20px;">
                            <h3 class="question-heading">Question {{$count}}:  {{$qa['question']}}</h3>
                        </div>
                        @if($qa['questionImages']->count() > 0)
                            <div class="question-image-lable">
                                <label>Question Images:</label>
                            </div>
                            <div class="image_preview">
                                @foreach($qa['questionImages'] as $images)
                                    <img src="{{ asset('storage/images/question-images/'.$images['question_image']) }}" alt="Question Image">
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="student-answer container" style="margin-top: 15px; margin-bottom: 15px;">
                        <div class="student-answer-lable" style="margin-bottom: 10px;">
                            <label>Answer:</label>
                        </div>
                        {!! $qa['submitted_answer'] !!}
                    </div>
                    @if(in_array($qa['answer_que_id'], $getStudentAttachment->pluck('question_id')->toArray()))
                        <div class="student-attachment container" style="margin-top: 15px; margin-bottom: 15px;">
                            <div class="student-attachment-lable">
                                <label>Student Attachments:</label>
                            </div>
                                @foreach ($getStudentAttachment as $attachment)
                                    <div class="submit-view">
                                        @if($qa['answer_que_id'] == $attachment['question_id'])
                                            <a href="{{asset('storage/assesment-submission/answer-documents/'.$attachment['student_enrol_id']."/".$attachment['assessment_id']."/".$attachment['question_id']."/".$attachment['submission_attchment'])}}">{{$attachment['submission_attchment']}}</a>
                                        @endif
                                    </div>
                                @endforeach
                        </div>
                    @endif
        
            @endforeach
        </div>
    </main>
</body>
</html>