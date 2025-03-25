<link href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css" />
<style>
    #show_hide_password .input-group-addon {border: 1px solid #dadce0;height: 50px;width: 50px;position: absolute;right: 0;display: flex;align-items: center;justify-content: center;background-color: #e9ecef;border-left: none;z-index: 9999;
    }
</style>
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title mt-0" id="myLargeModalLabel">Edit Student Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <form action="#" id="student_edit_form" method="POST" enctype="multipart/form-data">
        <div class="modal-body">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <input type="hidden" name="student_id" value="{{$student->id}}">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Student Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" value="{{ $student->name }}" name="name" id="name" placeholder="" />
                                        @error('name')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nric">Student NRIC <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" value="{{ $student->nric }}" name="nric" id="nric" placeholder="" />
                                        @error('nric')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email </label>
                                        <input type="text" class="form-control" value="{{ $student->email }}" name="email" id="email" placeholder="" />
                                        @error('email')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mobile_no">Mobile No </label>
                                        <input type="text" class="form-control" value="{{ $student->mobile_no }}" name="mobile_no" id="mobile_no" placeholder="" />
                                        @error('mobile_no')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group date-ico">
                                        <label for="dob">Date Of Birth </label>
                                        <input type="text" class="form-control singledate" value="{{ $student->dob }}" name="dob" id="dob" placeholder="" />
                                        @error('dob')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nationality">Nationality </label>
                                        <select class="form-control select2" required name="nationality" id="nationality">
                                            <option value="">Select Nationality</option>
                                            @foreach( getNationalityList() as $nationalitie )
                                                <option value="{{$nationalitie}}" {{ strtolower($student->nationality) == strtolower($nationalitie) ? 'selected' : '' }}>{{$nationalitie}}</option>
                                            @endforeach
                                        </select>
                                        @error('nationality')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <h4>Company Details</h4>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="company_sme">Company SME </label>
                                        <select class="form-control select2" name="company_sme" id="company_sme">
                                            <option value="Yes" {{ $student->company_sme == "Yes" ? 'selected' : '' }}>Yes</option>
                                            <option value="No" {{ $student->company_sme == "No" ? 'selected' : '' }}>No</option>
                                        </select>
                                        @error('company_sme')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_name">Company Name </label>
                                        <input type="text" class="form-control" value="{{ $student->company_name }}" name="company_name" id="company_name" placeholder="" />
                                        @error('company_name')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="company_uen">Company Uen </label>
                                        <input type="text" class="form-control" value="{{ $student->company_uen }}" name="company_uen" id="company_uen" placeholder="" />
                                        @error('company_uen')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="company_contact_person">Company Contact Person </label>
                                        <input type="text" class="form-control" value="{{ $student->company_contact_person }}" name="company_contact_person" id="company_contact_person" placeholder="" />
                                        @error('company_contact_person')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="company_contact_person_email">Company Contact Person Email </label>
                                        <input type="text" class="form-control" value="{{ $student->company_contact_person_email }}" name="company_contact_person_email" id="company_contact_person_email" placeholder="" />
                                        @error('company_contact_person_email')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="company_contact_person_number">Company Contact Person Number </label>
                                        <input type="text" class="form-control" value="{{ $student->company_contact_person_number }}" name="company_contact_person_number" id="company_contact_person_number" placeholder="" />
                                        @error('company_contact_person_number')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="billing_address">Address </label>
                                        <textarea id="billing_address" class="form-control" rows="3" name="billing_address">{{ $student->billing_address }}</textarea>
                                        @error('billing_address')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="meal_restrictions">Meal Restrictions</label>
                                        <select name="meal_restrictions" id="meal_restrictions" class="form-control select2">
                                            <option value="">Select option</option>
                                            <option value="Yes" {{ $student->meal_restrictions == "Yes" ? 'selected' : '' }}>Yes</option>
                                            <option value="No" {{ $student->meal_restrictions == "No" ? 'selected' : '' }}>No</option>
                                        </select>
                                        @error('meal_restrictions')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 {{ $student->meal_restrictions == 'Yes' ? '' : 'd-none' }}" id="meal_restrictions_type_div">
                                    <div class="form-group">
                                        <label for="meal_restrictions_type">Meal Restrictions Type</label>
                                        <select class="form-control select2" name="meal_restrictions_type" id="meal_restrictions_type">
                                            <option value="">Select option</option>
                                            @foreach( getMealRestrictionsType() as $mealkey => $mealType )
                                                <option value="{{$mealkey}}" {{ $student->meal_restrictions_type == $mealkey ? 'selected' : '' }}>{{$mealType}}</option>
                                            @endforeach
                                        </select>
                                        @error('meal_restrictions_type')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12 {{ $student->meal_restrictions_type == 'Other' ? '' : 'd-none' }}" id="meal_restrictions_other_div">
                                    <div class="form-group">
                                        <label for="meal_restrictions_other">Please specify meal restriction</label>
                                        <input class="form-control" value="{{ $student->meal_restrictions_other }}" name="meal_restrictions_other" id="meal_restrictions_other" />
                                        @error('meal_restrictions_other')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="student_password">Password</label>
                                        <div class="input-group" id="show_hide_password">
                                            <input type="password" class="form-control" name="password" id="student_password" placeholder="Type Student Password">
                                            <div class="input-group-addon">
                                              <a href=""><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                            </div>
                                          </div>
                                    </div>
                                </div>
                            </div>

                        </div><!--end card-body-->
                    </div><!--end card-->
                </div> <!--end col-->
            </div><!--end row-->

        </div>
        <div class="modal-footer">
            <button type="submit" id="submit_student_edit" class="btn btn-secondary waves-effect">Submit</button>
            <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Close</button>
        </div>
    </form>
</div>
<script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(".select2").select2({ width: '100%' });

        $(document).on('change', '#meal_restrictions', function(e) {
            e.preventDefault();
            let _val = $(this).val();
            if( _val == "Yes" ) {
                $('#meal_restrictions_type_div').removeClass('d-none');
                if( $('#meal_restrictions_type').val() == "Other" ) {
                    $('#meal_restrictions_other_div').removeClass('d-none');
                }
            } else {
                $('#meal_restrictions_type_div').addClass('d-none');
                $('#meal_restrictions_other_div').addClass('d-none');
            }
        });

        $(document).on('change', '#meal_restrictions_type', function(e) {
            e.preventDefault();
            let _val = $(this).val();
            if( _val == "Other" ) {
                $('#meal_restrictions_other_div').removeClass('d-none');
            } else {
                $('#meal_restrictions_other_div').addClass('d-none');
            }
        });

    });
    function initDateAndTimePicker() {
        $('.singledate').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD'
            },
            singleDatePicker: true,
            showDropdowns: true,
            maxDate: new Date(),
        });
    }
    initDateAndTimePicker();

    $(document).ready(function() {
    $("#show_hide_password a").on('click', function(event) {
        event.preventDefault();
        if($('#show_hide_password input').attr("type") == "text"){
            $('#show_hide_password input').attr('type', 'password');
            $('#show_hide_password i').addClass( "fa-eye-slash" );
            $('#show_hide_password i').removeClass( "fa-eye" );
        }else if($('#show_hide_password input').attr("type") == "password"){
            $('#show_hide_password input').attr('type', 'text');
            $('#show_hide_password i').removeClass( "fa-eye-slash" );
            $('#show_hide_password i').addClass( "fa-eye" );
        }
    });
});

</script>
