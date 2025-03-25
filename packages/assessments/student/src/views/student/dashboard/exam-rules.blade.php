<style>
    .fontsize-15{ font-size:15px; }  
    .primary-color { color: #658bf7; font-weight: 500; }
    .instructions-list { padding: 20px; margin-left: 20px; }
    .instructions-list li { font-size: 14px; }
    .assessment .card { width: 95%; margin: 0 auto; }
    .assessment .card .instructions-list { list-style-type: none; }
    .assessment .card .instructions-list li { position: relative; padding-left: 30px; padding-bottom: 15px; }
    .assessment .card .instructions-list li:last-child { padding-bottom: 0px; }
    .assessment .card .instructions-list li:before { content:"\f057"; position: absolute; top: -3px; left: 0px; font-size: 16px; font-family: 'Font Awesome 5 Free'; font-weight: 900; color: #FF0000; }
    .assessment .card-footer { background-color: transparent; margin: 0 auto; }
    .intro-heading { text-align: center; }

</style>

<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title mt-0" id="myLargeModalLabel">Exam Rules</h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <form action="#" id="coursemain_selection" method="POST" enctype="multipart/form-data">
        <div class="modal-body">
            <div class="row assessment">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="intro-heading">Please read the instructions below carefully before starting the exam:</h4>
                            <div class="col-md-12">
                                <ul class="instructions-list">
                                    <li>There should be no communication between candidates or external parties during the entire exam duration.</li>
                                    <li>Smartphones are not permitted to be used during the exam.</li>
                                    <li>Please inform the assessor if you need to leave the examination room for any reason.</li>
                                    <li>Please do not close the window before answer status show saved message under answer box.</li>
                                </ul>
                            </div>
                        </div><!--end card-body-->
                    </div><!--end card-->
                </div> <!--end col-->
            </div><!--end row-->

        </div>
        <div class="modal-footer">
            <a href="#" class="btn btn-primary" id="start-exam">Ready!</a>
        </div>
    </form>
</div>

<script>
$(document).ready(function(){
    $("#start-exam").click(function(){
        event.preventDefault();
        var baseUrl = '{{ route('student.assessment.exam', ["id" => $course_id, "studentid" => $student_id, "exam_id" => $exam_id, "assessment_id" => $assessment_id]) }}';
        var examwindow = window.open(baseUrl, '_self', '');
        examwindow.focus();

    });
});
</script>
