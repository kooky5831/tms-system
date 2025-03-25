@extends('admin.layouts.master')
@section('title', 'Edit Student Enrolment')
@push('css')
<link href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css" />
@endpush
@section('content')
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{route('admin.studentenrolment.list')}}">Student Enrolment</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Student Enrolment</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                {{-- <form action="{{ route('admin.studentenrolment.edit', $data->id) }}" method="POST" enctype="multipart/form-data"> --}}
                <form action="{{ route('admin.studentenrolment.edit', $data->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- <h5 class="card-header bg-secondary text-white mt-0">Edit Student Enrolment</h5> -->
                    <div class="card-body">
                        <h4 class="header-title mt-0">Basic Details</h4>
                        <div class="row">

                            <div class="col-md-6">
                                {{-- <label for="course_id">Selected Course Run </label> --}}
                                {{-- <input name="course_id" class="form-control" readonly value="{{$data->courseRun->course_start_date.'-'.$data->courseRun->course_end_date.' : '.$data->courseRun->courseMain->name." ".$data->courseRun->courseMain->reference_number}}" /> --}}
                                <label for="f2f_course_id">Selected Course Run <span class="text-danger">*</span></label>
                                <select name="f2f_course_id" id="f2f_course_id" required class="form-control select2">
                                    <option value="">Select Course</option>
                                    @foreach( $courseRunListService as $courseRun )
                                    <option value="{{$courseRun->id}}" {{$data->course_id == $courseRun->id ? 'selected' : ''}}>{{$courseRun->tpgateway_id." (".$courseRun->course_start_date.') - '. $courseRun->name." ".$courseRun->reference_number}}</option>
                                    @endforeach
                                </select>
                                @error('f2f_course_id')
                                    <label class="form-text text-danger">{{ $message }}</label>
                                @enderror
                            </div>

                            @if($singleCourse)
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sponsored_by_company">Sponsored By Company </label>
                                    <select class="form-control select2" name="sponsored_by_company" id="sponsored_by_company">
                                        <option value="Yes" {{ $data->sponsored_by_company == "Yes" ? 'selected' : '' }}>Yes</option>
                                        <option value="No (I'm signing up as an individual)" {{ $data->sponsored_by_company == "No (I'm signing up as an individual)" ? 'selected' : '' }}>No (I'm signing up as an individual)</option>
                                    </select>
                                    @error('sponsored_by_company')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tpgateway_refno">TPGateway Reference Number </label>
                                    <input type="text" class="form-control" readonly value="{{ $data->tpgateway_refno }}"  name="tpgateway_refno" id="tpgateway_refno" placeholder="" />
                                    @error('tpgateway_refno')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            @endif

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="xero_invoice_number">Xero Invoice Number </label>
                                    <input type="text" class="form-control" name="xero_invoice_number" placeholder="" value="{{ $data->xero_invoice_number }}" />
                                    {{-- <select class="form-control select2" id="xero_invoice_number" name="xero_invoice_number">
                                        <option value="{{ $data->xero_invoice_number }}" selected>{{ $data->xero_invoice_number }}</option>
                                    </select> --}}
                                </div>
                            </div>
                            
                            @if($data->courseRun->courseMain->course_full_fees != 0)
                                <div class="col-md-3">
                                    <label for="xero_invoice_number">Master invoice</label>
                                    <div class="d-flex">
                                        <div class="custom-control custom-radio mr-3">
                                            <input type="radio" class="custom-control-input" id="master_invoice_0" name="master_invoice" value="0" 
                                            {{$data->master_invoice == 0 ? "checked" : "" }}>
                                            <label  class="custom-control-label" for="master_invoice_0">Xero</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" id="master_invoice_1" name="master_invoice" value="1"
                                            {{$data->master_invoice == 1 ? "checked" : "" }}>
                                            <label  class="custom-control-label" for="master_invoice_1">TMS</label>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>

                        <div class="row">
                            @if($singleCourse)
                            <div class="col-md-3 {{ $data->sponsored_by_company != 'Yes' ? 'd-none' : '' }}" id="company_sme_div">
                                <div class="form-group">
                                    <label for="company_sme">Company SME </label>
                                    <select class="form-control select2" name="company_sme" id="company_sme">
                                        <option value="" >Select</option>
                                        <option value="Yes" {{ $data->company_sme == "Yes" ? 'selected' : '' }}>Yes</option>
                                        <option value="No" {{ $data->company_sme == "No" ? 'selected' : '' }}>No</option>
                                    </select>
                                    @error('company_sme')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="nationality">Nationality <span class="text-danger">*</span></label>
                                    <select class="form-control select2" required name="nationality" id="nationality">
                                        <option value="">Select Nationality</option>
                                        @foreach( getNationalityList() as $nationalitie )
                                            <option value="{{$nationalitie}}" {{ strtolower($data->nationality) == strtolower($nationalitie) ? 'selected' : '' }}>{{$nationalitie}}</option>
                                        @endforeach
                                    </select>
                                    @error('nationality')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="age">Age</label>
                                    <input type="text" class="form-control" value="{{ $data->age }}" name="age" id="age" placeholder="" />
                                    @error('age')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            @endif

                            {{-- <div class="col-md-3">
                                <div class="form-group">
                                    <label for="learning_mode">Learning Mode <span class="text-danger">*</span></label>
                                    <select class="form-control" required name="learning_mode" id="learning_mode">
                                        <option value="">Select Learning Mode</option>
                                        @foreach( getLearningMode() as $lmkey => $learningMode )
                                            <option value="{{$lmkey}}" {{ $data->learning_mode == $lmkey ? 'selected' : '' }}>{{$learningMode}}</option>
                                        @endforeach
                                    </select>
                                    @error('learning_mode')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div> --}}

                        </div>

                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name </label>
                                    <input type="text" class="form-control" readonly value="{{ $data->student->name }}" name="name" id="name" placeholder="" />
                                    @error('name')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nric">NRIC</label>
                                    <input type="text" class="form-control" readonly value="{{ $data->student->nric }}" name="nric" id="nric" placeholder="" />
                                    @error('nric')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="email">Email </label>
                                    <input type="text" class="form-control" value="{{ $data->email }}" name="email" id="email" placeholder="" />
                                    @error('email')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="mobile_no">Mobile No </label>
                                    <input type="text" class="form-control" value="{{ $data->mobile_no }}" name="mobile_no" id="mobile_no" placeholder="" />
                                    @error('mobile_no')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group date-ico">
                                    <label for="dob">Date Of Birth </label>
                                    <input type="text" class="form-control singledate" value="{{ $data->dob }}" name="dob" id="dob" placeholder="" />
                                    @error('dob')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        @if($singleCourse)
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="education_qualification">Education Qualification </label>
                                    <select class="form-control select2" name="education_qualification" id="education_qualification">
                                        <option value="">Select Education Qualification</option>
                                        @foreach( getEducationalQualificationsList() as $eduqualify )
                                            <option value="{{$eduqualify}}" {{ $data->education_qualification == $eduqualify ? 'selected' : '' }}>{{$eduqualify}}</option>
                                        @endforeach
                                    </select>
                                    @error('education_qualification')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <div class="row">

                            {{-- <div class="col-md-4">
                                <div class="form-group">
                                    <label for="designation">Designation </label>
                                    <select class="form-control" name="designation" id="designation">
                                        <option value="">Select Designation</option>
                                        @foreach( getDesignationList() as $designationName )
                                            <option value="{{$designationName}}" {{ $data->designation == $designationName ? 'selected' : '' }}>{{$designationName}}</option>
                                        @endforeach
                                    </select>
                                    @error('designation')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="salary">Salary Range </label>
                                    <select class="form-control" name="salary" id="salary">
                                        <option value="">Select Salary Range</option>
                                        @foreach( getSalaryRangeList() as $salaryRange )
                                            <option value="{{$salaryRange}}" {{ $data->salary == $salaryRange ? 'selected' : '' }}>{{$salaryRange}}</option>
                                        @endforeach
                                    </select>
                                    @error('salary')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div> --}}

                            <div class="col-md-3 company_details_div {{ $data->sponsored_by_company != 'Yes' ? 'd-none' : '' }}">
                                <div class="form-group">
                                    <label for="company_name">Company Name </label>
                                    <input type="text" class="form-control" value="{{ $data->company_name }}" name="company_name" id="company_name" placeholder="" />
                                    @error('company_name')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="pesa_refrerance_number">PSEA Referance No.</label>
                                    <input type="text" class="form-control" value="{{$data->pesa_refrerance_number}}" name="pesa_refrerance_number" id="pesa_refrerance_number" placeholder="" />
                                    @error('pesa_refrerance_number')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="skillfuture_credit">SkillsFuture Credit</label>
                                    <input type="text" class="form-control" value="{{$data->skillfuture_credit}}" name="skillfuture_credit" id="skillfuture_credit" placeholder="" />
                                    @error('skillfuture_credit')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="vendor_gov">Vendors@Gov</label>
                                    <input type="text" class="form-control" value="{{$data->vendor_gov}}" name="vendor_gov" id="vendor_gov" placeholder="" />
                                    @error('vendor_gov')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <div class="row company_details_div {{ $data->sponsored_by_company != 'Yes' ? 'd-none' : '' }}">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="company_uen">Company Uen </label>
                                    <input type="text" class="form-control" value="{{ $data->company_uen }}" name="company_uen" id="company_uen" placeholder="" />
                                    @error('company_uen')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="company_contact_person">Company Contact Person </label>
                                    <input type="text" class="form-control" value="{{ $data->company_contact_person }}" name="company_contact_person" id="company_contact_person" placeholder="" />
                                    @error('company_contact_person')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="company_contact_person_email">Company Contact Person Email </label>
                                    <input type="text" class="form-control" value="{{ $data->company_contact_person_email }}" name="company_contact_person_email" id="company_contact_person_email" placeholder="" />
                                    @error('company_contact_person_email')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="company_contact_person_number">Company Contact Person Number </label>
                                    <input type="text" class="form-control" value="{{ $data->company_contact_person_number }}" name="company_contact_person_number" id="company_contact_person_number" placeholder="" />
                                    @error('company_contact_person_number')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <div class="mt-4">
                            <h4 class="header-title mt-0">Billing Details</h4>
                        </div>
                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="billing_email">Billing Email <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $data->billing_email }}" required name="billing_email" id="billing_email" placeholder="" />
                                    @error('billing_email')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Billing Zip">Billing Zip</label>
                                    <input type="text" class="form-control" value="{{ $data->billing_zip }}" name="billing_zip" id="billing_zip" placeholder="" />
                                    @error('billing_zip')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="billing_address">Billing Address </label>
                                    <textarea id="billing_address" class="form-control" rows="3" name="billing_address">{{ $data->billing_address }}</textarea>
                                    @error('billing_address')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                        </div>
                        @endif

                        <div class="row">
                            @if($singleCourse)
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="billing_country">Billing Country</label>
                                    <select class="form-control select2" name="billing_country" id="billing_country">
                                        <option value="">Select Country</option>
                                        @foreach( getCountryList() as $country )
                                            <option value="{{$country}}" {{ $data->billing_country == $country ? 'selected' : '' }}>{{$country}}</option>
                                        @endforeach
                                    </select>
                                    @error('billing_country')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            @endif

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="remarks">Remarks</label>
                                    <textarea id="remarks" class="form-control" rows="3" name="remarks">{{ $data->remarks }}</textarea>
                                    @error('remarks')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="payment_mode">Payment Mode</label>
                                    <?php $selectedPaymentMode = "";
                                    if( !is_null($data->payment_mode_company) ) {
                                        $selectedPaymentMode = $data->payment_mode_company;
                                    } elseif( !is_null($data->payment_mode_individual) ) {
                                        $selectedPaymentMode = $data->payment_mode_individual;
                                    } elseif( !is_null($data->other_paying_by) ) {
                                        $selectedPaymentMode = $data->other_paying_by;
                                    } ?>
                                    <select class="form-control select2" value="{{ old('payment_mode') }}" name="payment_mode" id="payment_mode">
                                        <option value="">Select Payment Mode</option>
                                        @foreach( getModeOfPayment() as $paymentMode )
                                        <option value="{{$paymentMode}}" {{ $selectedPaymentMode == $paymentMode ? 'selected' : '' }}>{{$paymentMode}}</option>
                                        @endforeach
                                    </select>

                                    @error('payment_mode')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="amount">Nett Course Fee <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $data->amount }}" name="amount" id="amount" required="true" placeholder="" onkeypress="return isNumberKeyWithDecimal(event)" />
                                    @error('amount')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="discountAmount">Discount Amount</label>
                                    <input type="text" class="form-control" value="{{ $data->discountAmount }}" name="discountAmount" id="discountAmount" placeholder="" onkeypress="return isNumberKeyWithDecimal(event)" />
                                    @error('discountAmount')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            @if($singleCourse)
                            <div class="col-md-3">
                                <label class="my-1 control-label" for="payment_tpg_status">Payment TPG Status : </label>
                                <div class="form-group">
                                    <select class="form-control select2" name="payment_tpg_status" id="payment_tpg_status">
                                        @foreach( getPaymentStatus() as $paykey => $payStatus )
                                            <option value="{{$paykey}}" {{ $data->payment_tpg_status == $paykey ? 'selected' : '' }}>{{$payStatus}}</option>
                                        @endforeach
                                    </select>
                                    @error('payment_tpg_status')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            @endif

                            <div class="col-md-3">
                                <label class="my-1 control-label" for="payment_status">Payment Status : </label>
                                <div class="form-group">
                                    <select class="form-control select2" name="payment_status" id="payment_status">
                                        @foreach( getPaymentStatus() as $paykey => $payStatus )
                                            <option value="{{$paykey}}" {{ $data->payment_status == $paykey ? 'selected' : '' }}>{{$payStatus}}</option>
                                        @endforeach
                                    </select>
                                    @error('payment_status')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            {{-- <div class="col-md-3">
                                <label for="xero_nett_course_fees">Xero nett course fees <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{$data->xero_nett_course_fees}}" name="xero_nett_course_fees" id="xero_nett_course_fees" required="true" placeholder="" onkeypress="return isNumberKeyWithDecimal(event)" />
                                    @error('xero_nett_course_fees')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                            </div> --}}

                        </div>

                        <div class=row>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="paymentRemark">Payment Remark</label>
                                    <input type="text" class="form-control" value="{{ $data->payment_remark }}" name="payment_remark" id="payment_remark" placeholder=""/>
                                    @error('payment_remark')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="paymentRemark">Payment Due Date</label>
                                    <input type="text" class="form-control expected_date" value="{{ $data->due_date }}" name="due_date" id="due_date" placeholder=""/>
                                    @error('due_date')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="paymentRemark">Reference</label>
                                    <input type="text" class="form-control" value="{{$data->reference}}" name="reference" id="reference" placeholder=""/>
                                    @error('reference')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        @if($singleCourse)
                        <div class="mt-4">
                            <h4 class="header-title mt-0">Other Details</h4>
                        </div>

                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="meal_restrictions">Meal Restrictions</label>
                                    <select name="meal_restrictions" id="meal_restrictions" class="form-control select2">
                                        <option value="">Select option</option>
                                        <option value="Yes" {{ $data->meal_restrictions == "Yes" ? 'selected' : '' }}>Yes</option>
                                        <option value="No" {{ $data->meal_restrictions == "No" ? 'selected' : '' }}>No</option>
                                    </select>
                                    @error('meal_restrictions')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 {{ $data->meal_restrictions == "Yes" ? '' : 'd-none' }}" id="meal_restrictions_type_div">
                                <div class="form-group">
                                    <label for="meal_restrictions_type">Meal Restrictions Type</label>
                                    <select class="form-control select2" name="meal_restrictions_type" id="meal_restrictions_type">
                                        <option value="">Select option</option>
                                        @foreach( getMealRestrictionsType() as $mealkey => $mealType )
                                            <option value="{{$mealkey}}" {{ $data->meal_restrictions_type == $mealkey ? 'selected' : '' }}>{{$mealType}}</option>
                                        @endforeach
                                    </select>
                                    @error('meal_restrictions_type')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12 {{ $data->meal_restrictions_type == 'Other' ? '' : 'd-none' }}" id="meal_restrictions_other_div">
                                <div class="form-group">
                                    <label for="meal_restrictions_other">Please specify meal restriction</label>
                                    <input class="form-control" value="{{ $data->meal_restrictions_other }}" name="meal_restrictions_other" id="meal_restrictions_other" />
                                    @error('meal_restrictions_other')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="checkbox checkbox-primary">
                                        <input id="computer_navigation_skill" value="1" name="computer_navigation_skill" type="checkbox" {{ $data->computer_navigation_skill == 1 ? 'checked' : '' }}>
                                        <label for="computer_navigation_skill">I/the trainee have/has basic computer navigational skills such as opening and closing of files, dragging and dropping of widgets, copying and pasting of files</label>
                                    </div>
                                    @error('computer_navigation_skill')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="checkbox checkbox-primary">
                                        <input id="course_brochure_determined" value="1" name="course_brochure_determined" type="checkbox" {{ $data->course_brochure_determined == 1 ? 'checked' : '' }}>
                                        <label for="course_brochure_determined">I/the trainee have/has thoroughly read the course brochure and determined this course to be relevant to my current/future work</label>
                                    </div>
                                    @error('course_brochure_determined')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                        </div>
                        @endif
                    </div><!--end card-body-->
                    <div class="card-footer m-0 clearfix">
                        <button type="submit" class="btn btn-primary mar-r-10">Update</button>
                        <a href="{{ route('admin.studentenrolment.list') }}" class="btn btn-danger">Cancel</a>
                    </div>
                </form>
            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->

</div><!-- container -->
@endsection
@push("scripts")
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

        $(document).on('change', '#sponsored_by_company', function(e) {
            e.preventDefault();
            let _val = $(this).val();
            if( _val == "Yes" ) {
                $('#company_sme_div').removeClass('d-none');
                $('.company_details_div').removeClass('d-none');
            } else {
                $('#company_sme_div').addClass('d-none');
                $('.company_details_div').addClass('d-none');
            }
        });
    });
    $(function () {
        $("#xero_invoice_number").select2({
            placeholder: 'Search invoice number',
            multiple: false,
            minimumInputLength: 3,
            ajax: {
                url: "{{ route('admin.xero.get-xero-invoices') }}",
                type: "get",
                dataType: "JSON",
                data: function (params) {
                    return { q: params.term, /*search term*/ };
                },
                processResults: function (response) {
                    return { results: response };
                },
                delay: 250,
                cache: true
            },
        });

    });
    function initDateAndTimePicker() {
        $('.singledate').daterangepicker({
            locale: {
                format: 'Y-M-DD'
            },
            singleDatePicker: true,
            showDropdowns: true
        });
        $('.expected_date').daterangepicker({
            locale: {
                format: 'Y-M-DD'
            },
            singleDatePicker: true,
            showDropdowns: true,
        })
    }
    initDateAndTimePicker();
    
</script>
@endpush
