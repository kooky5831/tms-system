@extends('admin.layouts.master')
@section('title', 'Add Student Enrolment')
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
                        <li class="breadcrumb-item active">Add</li>
                    </ol>
                </div>
                <h4 class="page-title">Add Student Enrolment</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                @if( !empty($studentData->name) )
                <form action="{{ route('admin.studentenrolment.add', $studentData->id) }}" method="POST" enctype="multipart/form-data">
                @else
                <form action="{{ route('admin.studentenrolment.add') }}" method="POST" enctype="multipart/form-data">
                @endif
                    @csrf
                    <!-- <h5 class="card-header bg-secondary text-white mt-0">Add Student Enrolment</h5> -->
                    <div class="card-body">
                        <h4 class="header-title mt-0">Basic Details</h4>
                        <input type="hidden" name="learning_mode" id="learning_mode" value="f2f" />
                        <div class="row">

                            @if( !empty($studentData->name) )
                                <div class="col-md-6">
                                    <label for="f2f_course_id">Selected Course Run </label>
                                        @if( $studentData->course->courseMain->course_type_id == 1 )
                                        <input type="text" disabled="" name="course_id" class="form-control" value="{{$studentData->course->tpgateway_id." (".$studentData->course->course_start_date.') -'.$studentData->course->courseMain->name." ".$studentData->course->courseMain->reference_number}}" />
                                        @else
                                        <input type="text" disabled="" name="course_id" class="form-control" value="({{$studentData->course->course_start_date.') -'.$studentData->course->courseMain->name." ".$studentData->course->courseMain->reference_number}}" />
                                        @endif
                                    <input type="hidden" name="courses[0][f2f_course_id]" value="{{$studentData->course_id}}" />
                                    @error('f2f_course_id')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="amount">Nett Course Fee <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" value="{{ old('courses')[0]['amount'] }}" name="courses[0][amount]" id="amount" required="true" placeholder="" onkeypress="return isNumberKeyWithDecimal(event)" />
                                        @error('amount')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            @else
                            <div class="col-md-12">
                                <div class="repeater-custom-show-hide">
                                    <div data-repeater-list="courses">
                                        <div data-repeater-item="">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <label for="f2f_course_id">Select Course Run <span class="text-danger">*</span></label>
                                                    @if(!is_null($courseRunId))
                                                        <select name="courses[0][f2f_course_id]" required class="form-control select2 f2f_course_id">
                                                            <option value="{{$getCourseRun->id}}">{{$getCourseRun->tpgateway_id . " (" . $getCourseRun->course_start_date . ') - ' . $getCourseRun->courseMain->name . " " . $getCourseRun->courseMain->reference_number}}</option>
                                                        </select>
                                                    @else
                                                        <select name="courses[0][f2f_course_id]" required class="form-control select2 f2f_course_id">
                                                            <option value="">Select Course</option>
                                                        </select>
                                                    @endif

                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Nett Course Fee <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="courses[0][amount]" required="true" placeholder="" onkeypress="return isNumberKeyWithDecimal(event)" />
                                                        @error('amount')
                                                            <label class="form-text text-danger">{{ $message }}</label>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Xero Invoice Number</label>
                                                        <input type="text" class="form-control" name="courses[0][xero_invoice_number]" placeholder="" />
                                                    </div>
                                                </div>
                                                <div class="col-md-1 verti-cen">
                                                    <span data-repeater-delete="" class="btn btn-danger btn-sm">
                                                        <span class="far fa-trash-alt"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-0 text-center">
                                        <div class="col-sm-12">
                                            <span data-repeater-create="" class="btn btn-secondary btn-md reapet-add">
                                                <span class="white-add-ico"></span>  Add Another Course
                                            </span>
                                        </div><!--end col-->
                                    </div><!--end row-->
                                </div>
                            </div>
                            @endif
                        </div>

                        @if($singleCourse)
                            <div class="row">
                                <div class="col-md-3 onlysingle">
                                    <div class="form-group">
                                        <label for="sponsored_by_company">Sponsored By Company <span class="text-danger">*</span></label>
                                        <select class="form-control select2 toggleRequired" name="sponsored_by_company" id="sponsored_by_company" required="true">
                                            <option value="" >Select</option>
                                            <option value="Yes" {{ old('sponsored_by_company') == "Yes" ? 'selected' : '' }}>Yes</option>
                                            <option value="No (I'm signing up as an individual)" {{ old('sponsored_by_company') == "No (I'm signing up as an individual)" ? 'selected' : '' }}>No (I'm signing up as an individual)</option>
                                        </select>
                                        @error('sponsored_by_company')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3 {{ old('sponsored_by_company') != 'Yes' ? 'd-none' : '' }}" id="company_sme_div">
                                    <div class="form-group">
                                        <label for="company_sme">Company SME</label>
                                        <select class="form-control select2" name="company_sme" id="company_sme">
                                            <option value="" >Select</option>
                                            <option value="Yes" {{ old('company_sme') == "Yes" ? 'selected' : '' }}>Yes</option>
                                            <option value="No" {{ old('company_sme') == "No" ? 'selected' : '' }}>No</option>
                                        </select>
                                        @error('company_sme')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>

                                {{-- <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="name">TPGateway Reference Number </label>
                                        <input type="text" class="form-control" value="{{ old('tpgateway_refno') }}"  name="tpgateway_refno" id="tpgateway_refno" placeholder="" />
                                        @error('tpgateway_refno')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div> --}}
                            </div>
                        @endif

                        <div class="row">
                            @if($singleCourse)
                            <div class="col-md-4 onlysingle">
                                <div class="form-group">
                                    <label for="nationality">Nationality <span class="text-danger">*</span></label>
                                    <select class="form-control toggleRequired select2" name="nationality" id="nationality" required="true">
                                        <option value="">Select Nationality</option>
                                        @foreach( getNationalityList() as $nationalitie )
                                            <option value="{{$nationalitie}}" {{ old('nationality') == $nationalitie ? 'selected' : '' }}>{{$nationalitie}}</option>
                                        @endforeach
                                    </select>
                                    @error('nationality')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-2 onlysingle">
                                <div class="form-group">
                                    <label for="age">Age</label>
                                    <input type="text" class="form-control" value="{{ old('age') }}" name="age" id="age" placeholder="" />
                                    @error('age')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            @endif
                            {{-- <div class="col-md-2">
                                <div class="form-group">
                                    <label for="learning_mode">Learning Mode <span class="text-danger">*</span></label>
                                    <select class="form-control" required name="learning_mode" id="learning_mode">
                                        <option value="">Select Learning Mode</option>
                                        @foreach( getLearningMode() as $lmkey => $learningMode )
                                            <option value="{{$lmkey}}" {{ old('learning_mode') == $lmkey ? 'selected' : '' }}>{{$learningMode}}</option>
                                        @endforeach
                                    </select>
                                    @error('learning_mode')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div> --}}

                       </div>

                       <div class="mt-4">
                            <h4 class="header-title mt-0">Student Details</h4>
                        </div>

                       <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    @if( !empty($studentData->name) )
                                    <input type="text" class="form-control" value="{{ $studentData->name }}" name="name" id="name" placeholder="" />
                                    @else
                                    <input type="text" class="form-control" value="{{ old('name') }}" name="name" id="name" placeholder="" />
                                    @endif
                                    @error('name')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nric">NRIC <span class="text-danger">*</span></label>
                                    @if( !empty($studentData->nric) )
                                    <input type="text" class="form-control" value="{{ $studentData->nric }}" name="nric" id="nric" placeholder="" />
                                    @else
                                    <input type="text" class="form-control" value="{{ old('nric') }}" name="nric" id="nric" placeholder="" />
                                    @endif
                                    @error('nric')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                        </div>

                       <div class="row">

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    @if( !empty($studentData->email) )
                                    <input type="text" class="form-control" value="{{ $studentData->email }}" name="email" id="email" placeholder="" />
                                    @else
                                    <input type="text" class="form-control" value="{{ old('email') }}" name="email" id="email" placeholder="" />
                                    @endif
                                    @error('email')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="mobile_no">Mobile No </label>
                                    @if( !empty($studentData->mobile) )
                                    <input type="text" class="form-control" value="{{ $studentData->mobile }}" name="mobile_no" id="mobile_no" placeholder="" />
                                    @else
                                    <input type="text" class="form-control" value="{{ old('mobile_no') }}" name="mobile_no" id="mobile_no" placeholder="" />
                                    @endif
                                    @error('mobile_no')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="dob">Date Of Birth </label>
                                    <input type="text" class="form-control singledate" value="{{ old('dob') }}" name="dob" id="dob" placeholder="" />
                                    @error('dob')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="pesa_refrerance_number">PSEA Referance No.</label>
                                    <input type="text" class="form-control" value="{{ old('pesa_refrerance_number') }}" name="pesa_refrerance_number" id="pesa_refrerance_number" placeholder="" />
                                    @error('pesa_refrerance_number')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="skillfuture_credit">SkillsFuture Credit</label>
                                    <input type="text" class="form-control" value="{{old('skillfuture_credit')}}" name="skillfuture_credit" id="skillfuture_credit" placeholder="" />
                                    @error('skillfuture_credit')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="vendor_gov">Vendors@Gov</label>
                                    <input type="text" class="form-control" value="{{old('vendor_gov')}}" name="vendor_gov" id="vendor_gov" placeholder="" />
                                    @error('vendor_gov')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        @if($singleCourse)
                        <div class="row onlysingle">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="education_qualification">Education Qualification </label>
                                    <select class="form-control select2" name="education_qualification" id="education_qualification">
                                        <option value="">Select Education Qualification</option>
                                        @foreach( getEducationalQualificationsList() as $eduqualify )
                                            <option value="{{$eduqualify}}" {{ old('education_qualification') == $eduqualify ? 'selected' : '' }}>{{$eduqualify}}</option>
                                        @endforeach
                                    </select>
                                    @error('education_qualification')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <div class="row onlysingle">

                            {{-- <div class="col-md-4">
                                <div class="form-group">
                                    <label for="designation">Designation </label>
                                    <select class="form-control" name="designation" id="designation">
                                        <option value="">Select Designation</option>
                                        @foreach( getDesignationList() as $designationName )
                                            <option value="{{$designationName}}" {{ old('designation') == $designationName ? 'selected' : '' }}>{{$designationName}}</option>
                                        @endforeach
                                    </select>
                                    @error('designation')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="salary">Salary Range</label>
                                    <select class="form-control" name="salary" id="salary">
                                        <option value="">Select Salary Range</option>
                                        @foreach( getSalaryRangeList() as $salaryRange )
                                            <option value="{{$salaryRange}}" {{ old('salary') == $salaryRange ? 'selected' : '' }}>{{$salaryRange}}</option>
                                        @endforeach
                                    </select>
                                    @error('salary')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div> --}}

                            <div class="col-md-4 company_details_div {{ old('sponsored_by_company') != 'Yes' ? 'd-none' : '' }}">
                                <div class="form-group">
                                    <label for="company_name">Company Name </label>
                                    <input type="text" class="form-control" value="{{ old('company_name') }}" name="company_name" id="company_name" placeholder="" />
                                    @error('company_name')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <div class="row onlysingle company_details_div {{ old('sponsored_by_company') != 'Yes' ? 'd-none' : '' }}">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="company_uen">Company UEN </label>
                                    <input type="text" class="form-control" value="{{ old('company_uen') }}" name="company_uen" id="company_uen" placeholder="" />
                                    @error('company_uen')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="company_contact_person">Company Contact Person </label>
                                    <input type="text" class="form-control" value="{{ old('company_contact_person') }}" name="company_contact_person" id="company_contact_person" placeholder="" />
                                    @error('company_contact_person')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="company_contact_person_email">Company Contact Person Email </label>
                                    <input type="text" class="form-control" value="{{ old('company_contact_person_email') }}" name="company_contact_person_email" id="company_contact_person_email" placeholder="" />
                                    @error('company_contact_person_email')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="company_contact_person_number">Company Contact Person Number </label>
                                    <input type="text" class="form-control" value="{{ old('company_contact_person_number') }}" name="company_contact_person_number" id="company_contact_person_number" placeholder="" />
                                    @error('company_contact_person_number')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            
                        </div>
                        @endif

                        @if($singleCourse)
                        <div class="mt-4 onlysingle">
                            <h4 class="header-title mt-0">Billing Details</h4>
                        </div>

                        <div class="row onlysingle">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="billing_email">Billing Email <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control toggleRequired" value="{{ old('billing_email') }}" name="billing_email" id="billing_email" required="true" placeholder="" />
                                    @error('billing_email')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Billing Zip">Billing Zip</label>
                                    <input type="text" class="form-control" value="{{ old('billing_zip') }}" name="billing_zip" id="billing_zip" placeholder="" />
                                    @error('billing_zip')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="billing_address">Billing Address</label>
                                    <textarea id="billing_address" class="form-control" rows="3" name="billing_address">{{ old('billing_address') }}</textarea>
                                    @error('billing_address')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                        </div> 
                        @endif

                        <div class="row">
                            @if($singleCourse)
                            <div class="col-md-6 onlysingle">
                                <div class="form-group">
                                    <label for="billing_country">Billing Country</label>
                                    <select class="form-control select2" name="billing_country" id="billing_country">
                                        <option value="">Select Country</option>
                                        <?php $selectedCountry = old('billing_country') ? old('billing_country') : 'Singapore'; ?>
                                        @foreach( getCountryList() as $country )
                                            <option value="{{$country}}" {{ $selectedCountry == $country ? 'selected' : '' }}>{{$country}}</option>
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
                                    <textarea id="remarks" class="form-control" rows="3" name="remarks">{{ old('remarks') }}</textarea>
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
                                    <select class="form-control select2" value="{{ old('payment_mode') }}" name="payment_mode" id="payment_mode">
                                        <option value="">Select Payment Mode</option>
                                        @foreach( getModeOfPayment() as $paymentMode )
                                        <option value="{{$paymentMode}}">{{$paymentMode}}</option>
                                        @endforeach
                                    </select>

                                    @error('payment_mode')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="discountAmount">Discount Amount</label>
                                    <input type="text" class="form-control" value="{{ old('discountAmount') }}" name="discountAmount" id="discountAmount" placeholder="" onkeypress="return isNumberKeyWithDecimal(event)" />
                                    @error('discountAmount')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            @if($singleCourse)
                            <div class="col-md-3 onlysingle">
                                <label class="my-1 control-label" for="payment_tpg_status">Payment TPG Status : </label>
                                <div class="form-group">
                                    <select class="form-control select2" name="payment_tpg_status" id="payment_tpg_status">
                                        @foreach( getPaymentStatus() as $paykey => $payStatus )
                                            <option value="{{$paykey}}" {{ old('payment_tpg_status') == $paykey ? 'selected' : '' }}>{{$payStatus}}</option>
                                        @endforeach
                                    </select>
                                    @error('payment_tpg_status')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            @endif

                            <div class="col-md-3">
                                <label class="my-1 control-label" for="payment_status">Payment Status : <span class="text-danger">*</span></label>
                                <div class="form-group">
                                    <select class="form-control select2" name="payment_status" id="payment_status">
                                        @foreach( getPaymentStatus() as $paykey => $payStatus )
                                            <option value="{{$paykey}}" {{ old('payment_status') == $paykey ? 'selected' : '' }}>{{$payStatus}}</option>
                                        @endforeach
                                    </select>
                                    @error('payment_status')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <div class=row>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="paymentRemark">Payment Remark</label>
                                    <input type="text" class="form-control" value="{{ old('payment_remark') }}" name="payment_remark" id="payment_remark" placeholder=""/>
                                    @error('payment_remark')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="paymentRemark">Payment Due Date</label>
                                    <input type="text" class="form-control expected_date" value="{{ old('due_date') }}" name="due_date" id="due_date" placeholder=""/>
                                    @error('due_date')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="paymentRemark">Reference</label>
                                    <input type="text" class="form-control" value="{{ old('reference') }}" name="reference" id="reference" placeholder=""/>
                                    @error('reference')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>                            
                        </div>

                        @if($singleCourse)
                        <div class="mt-4 onlysingle">
                            <h4 class="header-title mt-0">Other Details</h4>
                        </div>

                        <div class="row onlysingle">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="meal_restrictions">Meal Restrictions</label>
                                    <select name="meal_restrictions" id="meal_restrictions" class="form-control select2">
                                        <option value="">Select option</option>
                                        <option value="Yes" {{ old('meal_restrictions') == "Yes" ? 'selected' : '' }}>Yes</option>
                                        <option value="No" {{ old('meal_restrictions') == "No" ? 'selected' : '' }}>No</option>
                                    </select>
                                    @error('meal_restrictions')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6 {{ old('meal_restrictions') == 'Yes' ? '' : 'd-none' }}" id="meal_restrictions_type_div">
                                <div class="form-group">
                                    <label for="meal_restrictions_type">Meal Restrictions Type</label>
                                    <select class="form-control select2" name="meal_restrictions_type" id="meal_restrictions_type">
                                        <option value="">Select option</option>
                                        @foreach( getMealRestrictionsType() as $mealkey => $mealType )
                                            <option value="{{$mealkey}}" {{ old('meal_restrictions_type') == $mealkey ? 'selected' : '' }}>{{$mealType}}</option>
                                        @endforeach
                                    </select>
                                    @error('meal_restrictions_type')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12 {{ old('meal_restrictions_type') == 'Other' ? '' : 'd-none' }}" id="meal_restrictions_other_div">
                                <div class="form-group">
                                    <label for="meal_restrictions_other">Please specify meal restriction</label>
                                    <input class="form-control" value="{{ old('meal_restrictions_other') }}" name="meal_restrictions_other" id="meal_restrictions_other" />
                                    @error('meal_restrictions_other')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="checkbox checkbox-primary">
                                        <input id="computer_navigation_skill" value="1" name="computer_navigation_skill" type="checkbox" {{ old('computer_navigation_skill') ? 'checked' : '' }}>
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
                                        <input id="course_brochure_determined" value="1" name="course_brochure_determined" type="checkbox" {{ old('course_brochure_determined') ? 'checked' : '' }}>
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
                        <button type="submit" class="btn btn-primary mar-r-10">Submit</button>
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
<script src="{{ asset('assets/plugins/repeater/jquery.repeater.min.js') }}"></script>
<script type="text/javascript">
    function formatResult(opt) {
        if (!opt.id) {
            return opt.text;
        }
        var $opt = $( '<span record="'+opt+'">'+ opt.text + '</span>');
        if( opt.coursetype != 1 ) {
            // hide all
            $('.onlysingle').addClass('d-none');
            $('.toggleRequired').prop('required', false);
        } else {
            // unhide all
            $('.onlysingle').removeClass('d-none');
            $('.toggleRequired').prop('required', true);
        }
        return $opt;
    };

    function formatSelection(opt) {
        return opt.text;
    }

    function setRepeter() {

        let $repeater = $('.repeater-custom-show-hide').repeater({
            // initEmpty: true,
            isFirstItemUndeletable: true,
            show: function () {
                $(this).slideDown();
                initCourseSearch();
            },
            hide: function (remove) {
              if (confirm('Are you sure you want to remove this item?')) {
                $(this).slideUp(remove);
              }
            }
        });
    }

    function initCourseSearch() {
        $(".select2").select2({ width: '100%' });

        $(".f2f_course_id").select2({
            placeholder: 'Search Courses',
            // multiple: true,
            // minimumInputLength: 3,
            width: '100%',
            templateResult: formatResult,
            templateSelection: formatSelection,
            ajax: {
                url: "{{ route('admin.ajax.search.courseruns') }}",
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
    }

    $(document).ready(function() {
        setRepeter();
        initCourseSearch();

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

    function initDateAndTimePicker() {
        $('.singledate').daterangepicker({
            locale: {
                format: 'Y-M-DD'
            },
            singleDatePicker: true,
            showDropdowns: true,
        });
        
        $('.expected_date').daterangepicker({
            locale: {
                format: 'Y-M-DD'
            },
            singleDatePicker: true,
            showDropdowns: true,
            minDate: moment().add(7, 'd'),
        })
    }
    initDateAndTimePicker();


</script>
@endpush
