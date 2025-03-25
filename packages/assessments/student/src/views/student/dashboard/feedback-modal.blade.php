<style>
    .fontsize-15{ font-size:15px; }  
    .primary-color { color: #658bf7; font-weight: 500; }
    .assessment .card { width: 95%; margin: 0 auto; }
    .assessment .card .instructions-list { list-style-type: none; }
    .assessment .card .instructions-list li { position: relative; padding-left: 30px; padding-bottom: 15px; }
    .assessment .card .instructions-list li:last-child { padding-bottom: 0px; }
    .assessment .card .instructions-list li:before { content:"\f057"; position: absolute; top: -3px; left: 0px; font-size: 16px; font-family: 'Font Awesome 5 Free'; font-weight: 900; color: #FF0000; }
    .assessment .card-footer { background-color: transparent; margin: 0 auto; }
    .intro-heading { text-align: center; }
    /* Hide the browser's default checkbox */
    .allcustomchecks input { margin-left: -250px; opacity: 0; cursor: pointer; }
    .allcheckmarks { position: absolute;top: -5px;left: 264px;height: 32px;width: 32px; background-color: #eee; border-radius: 5px; }
    /* On mouse-over, add a grey background color */
    .allcustomchecks:hover input ~ .allcheckmarks { background-color: #ccc; }
    /* When the checkbox is checked, add a blue background */
    .allcustomchecks input:checked ~ .allcheckmarks { background-color: #fa5e37;border-radius: 5px; } 
    /* Create the checkmark/indicator (hidden when not checked) */
    .allcheckmarks:after { content: "";position: absolute;display: none; }
    /* Show the checkmark when checked */
    .allcustomchecks input:checked ~ .allcheckmarks:after { display: block; }
    /* Style the checkmark/indicator */
    .allcustomchecks .allcheckmarks:after {left: 13px; top: 7px;width: 7px;height: 15px;border: solid white;border-width: 0 3px 3px 0;-webkit-transform: rotate(45deg);-ms-transform: rotate(45deg);transform: rotate(45deg);}
    /*custom checkbox end*/

</style>

<div id="feedback-modal" class="modal fade" role="dialog">  
    <div class="modal-body">
        <div class="row link-form">
            <div class="col-12 feedback">
                <div class="feedback-inner">
                    <div>
                        <h4 class="intro-heading mb-lg-5 mb-4">Click on link and submit your feedback</h4>
                        <div class="col-md-12 text-center">
                            <div class="instructions-list">
                                <p>Please click on the link to submit your TRAQOM feedback and upload the screenshot of your successful submission before starting your assessment. <strong> The Course Run ID can be retrieved from the whiteboard or the trainer. </strong> </p>
                                <p>If you have benefitted from this course, we greatly appreciate your positive and constructive feedback as this will help others like yourself benefit from the same course.</p>
                                <p>Thank you for your unwavering support!</p>
                            </div>
                            <a href="https://ssgtraqom.qualtrics.com/jfe/form/SV_3K9i7rTJ9OLsauW?Q_CHL=qr" target="blank" class="btn btn-primary btn-large mt-3 link-btn">TRAQOM feedback</a>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        <div class="row upload-form" style="display: none">
            <div class="col-12 feedback">
                <div class="feedback-inner">
                    <div>
                        <a class="back-to-link">
                            <svg version="1.1" id="fi_545680" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
                                <g>
                                    <g>
                                        <path d="M492,236H68.442l70.164-69.824c7.829-7.792,7.859-20.455,0.067-28.284c-7.792-7.83-20.456-7.859-28.285-0.068
                                            l-104.504,104c-0.007,0.006-0.012,0.013-0.018,0.019c-7.809,7.792-7.834,20.496-0.002,28.314c0.007,0.006,0.012,0.013,0.018,0.019
                                            l104.504,104c7.828,7.79,20.492,7.763,28.285-0.068c7.792-7.829,7.762-20.492-0.067-28.284L68.442,276H492
                                            c11.046,0,20-8.954,20-20C512,244.954,503.046,236,492,236z"></path>
                                    </g>
                                </g>
                            </svg>
                        Back </a>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <div class="form-group">
                                    <h4>Have you done your TRAQOM survey?</h4>
                                    {{-- <img src="" alt="feedback_image" id="feedback_image" class="" style="width:auto; height:160px; display:none" >
                                     --}}
                                     <div class="p-3">
                                         <p class="font-16">It is mandatory for SSG-registered training providers to ensure all learners complete the TRAQOM Survey. Thank you for helping us improve the course by sharing your valuable feedback!</p>
                                     </div>
                                    <div class="custom-file">
                                        {{-- <input type="file" accept="image/*" name="screenshot" class="custom-file-input" id="screenshot">
                                        <label class="custom-file-label" for="screenshot">Choose File</label> --}}
                                        {{-- <input type="checkbox" id="vehicle1" name="vehicle1" value="Bike"> --}}
                                        <label class="allcustomchecks">
                                            <input type="checkbox" class="multiCheck" name="is_feedback_submitted">
                                            <span class="allcheckmarks"></span>
                                        </label>
                                        <label for="survey" class="font-15"> I have completed the TRAQOM Survey</label><br>
                                    </div>
                                </div>
                                <button class="btn-primary btn mt-2 upload-ss">Submit Feedback</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
</script>
