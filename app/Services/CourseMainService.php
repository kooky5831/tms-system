<?php

namespace App\Services;

use App\Models\CourseMain;
use App\Models\XeroCourseLineItems;
use Illuminate\Support\Facades\Storage;
use App\Models\CourseAssessments;
use Auth;

class CourseMainService
{
    protected $courseMain_model;

    public function __construct()
    {
        $this->courseMain_model = new CourseMain;
    }

    public function getAllCourseMain($request)
    {
        $coursemain = $this->courseMain_model->with(['coursetype', 'courseTags']);

        $courseModule = $request->get('course_module');
        $courseType = $request->get('course_type');
        $courseMode = $request->get('course_mode');
        $courseTags = $request->get('course_tags');


        if( !empty($courseModule) ) {
            $coursemain->WhereHas('coursetype',function ($query) use ($courseModule) {
                $query->Where('course_types.id', $courseModule);
            });
        }

        if( !empty($courseType) ) {
            $coursemain->Where('course_type', $courseType);
        }

        if( !empty($courseMode) ) {
            $coursemain->Where('course_mode_training', $courseMode);
        }

        if( !empty($courseTags) ) {
            $coursemain->WhereHas('courseTags',function ($query) use ($courseTags) {
                $query->Where('course_tags.id', $courseTags);
            });
        }
        return $coursemain;
    }

    public function getAllCourseMainList()
    {
        return $this->courseMain_model->get();
    }

    public function getAllCourseMainListForRuns()
    {
        return $this->courseMain_model->where('course_type_id', '!=', CourseMain::MODULAR_COURSE)->get();
    }

    public function getCourseMainById($id)
    {
        return $this->courseMain_model->with(['trainers', 'lineItems', 'programTypes', 'assessments'])->find($id);
    }

    public function registerCourseMain($request)
    {
        $record = $this->courseMain_model;

        $record->course_type_id             = $request->get('course_type_id');
        $record->name                       = $request->get('name');
        $record->reference_number           = $request->get('reference_number');
        if( $request->has('skill_code') ) {
            $record->skill_code                 = $request->get('skill_code');
        }
        $record->course_full_fees           = $request->get('course_full_fees');
        $record->course_type                = $request->get('course_type');
        $record->course_mode_training       = $request->get('course_mode_training');

        if( $request->has('coursesmain') && count($request->get('coursesmain')) ) {
            $record->single_course_ids      = implode (",", $request->get('coursesmain'));
        }

        if( $request->hasfile('coursefileimage') ) {
            // upload file
            $uploadedFile = $request->file('coursefileimage');
            $filename = "certificate_".rand(1000,9999)."_".time().".".$uploadedFile->getClientOriginalExtension();
            Storage::putFileAs('public/images/course_certificate', $uploadedFile, $filename);

            // Storage::disk('public_course_certificate_uploads')->putFileAs(
            //     config('uploadpath.course_certificate'),
            //     $uploadedFile,
            //     $filename
            // );
            $record->certificate_file = $filename;
            $record->cert_cordinates  = $request->get('cords');
        }

        if( $request->has('brandingTheme') ) {
            $record->branding_theme_id = $request->get('brandingTheme');
        }

        $record->created_by                 = Auth::Id();
        $record->updated_by                 = Auth::Id();

        //Save course abbreviation
        if($request->has('course_abbreviation')){
            $record->course_abbreviation = $request->get('course_abbreviation');
        }

        // Save shared drive Folder Id
        if( $request->has('shared_drive_id') ) {
            $record->shared_drive_id = $request->get('shared_drive_id');
        }

        // Save Trainer Folder Id
        if( $request->has('trainer_folder_id') ) {
            $record->trainer_folder_id = $request->get('trainer_folder_id');
        }

        // Save Reference Document File Id
        if( $request->has('doc_file_id') ) {
            $record->doc_file_id = $request->get('doc_file_id');
        }

        // Save Reference Spreadsheet File Id
        if( $request->has('spreadsheet_file_id') ) {
            $record->spreadsheet_file_id = $request->get('spreadsheet_file_id');
        }

        // Save Reference Assessment File Id
        if( $request->has('assessment_file_id') ) {
            $record->assessment_file_id = $request->get('assessment_file_id');
        }

        // Save Reference Attendance File Id
        if( $request->has('attendance_file_id') ) {
            $record->attendance_file_id = $request->get('attendance_file_id');
        }

        // Save Assessment Records ShortURL
        if( $request->has('assessment_short_title') ) {
            $record->assessment_short_title = $request->get('assessment_short_title');
        }

        // Save Application fees
        $request->has('application_fees') ? $record->application_fees = CourseMain::APP_FEES_TRUE : $record->application_fees = CourseMain::APP_FEES_FALSE;

        $record->save();

        /* Save Course Assessment Start */

        

        $courseId = $record->id;
        if( !empty($request->get('assessments') )) {
            $assessmentsCount = count($request->get('assessments'));
            if( $assessmentsCount > 0 ) {
                for ($i = 0; $i < $assessmentsCount; $i++) {
                    
                    // add to assessment
                    $assessments = new CourseAssessments;
                    $assessments->course_id             = $courseId;
                    $assessments->assessment_file_title = $request->get('assessments')[$i]['assessment_file_title'];
                    $assessments->assessment_file_id    = $request->get('assessments')[$i]['assessment_file_id'];
                    $assessments->start_time            = $request->get('assessments')[$i]['start_time'];
                    $assessments->end_time              = $request->get('assessments')[$i]['end_time'];
                    $assessments->short_url             = $request->get('assessments')[$i]['short_url'];
                    $assessments->created_by            = Auth::Id();
                    $assessments->updated_by            = Auth::Id();
                    $assessments->save();
                }
            }
        }
        

        /* Save Course Assessment End */

        /*if($request->has('program_type_id') && !empty($request->get('program_type_id')[0])) {
            $record->programTypes()->attach($request->get('program_type_id')[0]); // add user
        }*/

        if($request->has('program_type_id') && !empty($request->get('program_type_id'))) {
            $record->programTypes()->attach($request->get('program_type_id')); // add user
        }

        // if( $request->has('items') ) {
        //     $items = $request->get('items');
        //     foreach( $items as $item ) {
        //         $recordItem = new XeroCourseLineItems;
        //         $recordItem->course_main_id = $record->id;
        //         foreach( $xeroitems as $xitem ) {
        //             if( $xitem['code'] == $item ) {
        //                 $recordItem->code = $xitem['code'];
        //                 $recordItem->name = $xitem['name'];
        //                 $recordItem->description = $xitem['description'];
        //                 $recordItem->amount = $xitem['sales_details']['unit_price'];
        //                 $recordItem->account_code = $xitem['sales_details']['account_code'];
        //                 break;
        //             }
        //         }
        //         $recordItem->save();
        //     }
        // }

        $record->trainers()->attach($request->get('coursetrainers'));
        $record->courseTags()->attach($request->get('coursetag'));
        return $record;
    }

    public function updateCourseMain($id, $request)
    {
        $record = $this->getCourseMainById($id);

        if( $record ) {

            $record->course_type_id             = $request->get('course_type_id');
            $record->name                       = $request->get('name');
            $record->reference_number           = $request->get('reference_number');
            if( $request->has('skill_code') ) {
                $record->skill_code                 = $request->get('skill_code');
            }
            $record->course_full_fees           = $request->get('course_full_fees');
            $record->course_type                = $request->get('course_type');
            $record->course_mode_training       = $request->get('course_mode_training');
            if( $record->course_type_id == CourseMain::MODULAR_COURSE && $request->get('course_type_id') != CourseMain::MODULAR_COURSE ) {
                $record->single_course_ids      = NULL;
            }
            if( $request->has('coursesmain') && count($request->get('coursesmain')) ) {
                $record->single_course_ids      = implode (",", $request->get('coursesmain'));
            }

            if( $request->hasfile('coursefileimage') ) {
                // upload file
                $uploadedFile = $request->file('coursefileimage');
                $filename = "certificate_".rand(1000,9999)."_".time().".".$uploadedFile->getClientOriginalExtension();
                Storage::putFileAs('public/images/course_certificate', $uploadedFile, $filename);

                // Storage::disk('public_course_certificate_uploads')->putFileAs(
                //     config('uploadpath.course_certificate'),
                //     $uploadedFile,
                //     $filename
                // );
                $record->certificate_file = $filename;
            }
            
            if( $request->has('no_funding') && !empty($request->get('no_funding')) ) {
                $record->no_funding  = $request->get('no_funding');
            }
            
            if($request->has('is_grant_active')){
                if( $request->has('enhanced_funding') ) {
                    $record->enhanced_funding  = $request->get('enhanced_funding');
                }
                if( $request->has('baseline_funding') ) {
                    $record->baseline_funding  = $request->get('baseline_funding');
                }           
                if( $request->has('gst') ) {
                    $record->gst  = $request->get('gst');
                }
                if( $request->has('gst_applied_on') ) {
                    $record->gst_applied_on  = $request->get('gst_applied_on');
                }
                $record->is_grant_active = $request->is_grant_active;
            } else {
                $record->is_grant_active = 0;
                $record->enhanced_funding = null;
                $record->baseline_funding = null;
                if( $request->has('without_grant_gst') ) {
                    $record->gst  = $request->get('without_grant_gst');
                }
                if( $request->has('without_grant_gst_applied_on') ) {
                    $record->gst_applied_on  = $request->get('without_grant_gst_applied_on');
                }
            }
            


            
            if( $request->has('cords') && !empty($request->get('cords')) ) {
                $record->cert_cordinates  = $request->get('cords');
            }

            if( $request->has('brandingTheme') ) {
                $record->branding_theme_id = $request->get('brandingTheme');
            }

            $record->updated_by = Auth::Id();

            //Save course abbreviation
            if($request->has('course_abbreviation')){
                $record->course_abbreviation = $request->get('course_abbreviation');
            }

            // Update shared drive Folder Id
            if( $request->has('shared_drive_id') ) {
                $record->shared_drive_id = $request->get('shared_drive_id');
            }

            // Update Trainder Folder Id
            if( $request->has('trainer_folder_id') ) {
                $record->trainer_folder_id = $request->get('trainer_folder_id');
            }

            // Update Reference Document File Id
            if( $request->has('doc_file_id') ) {
                $record->doc_file_id = $request->get('doc_file_id');
            }

            // Update Reference Spreadsheet File Id
            if( $request->has('spreadsheet_file_id') ) {
                $record->spreadsheet_file_id = $request->get('spreadsheet_file_id');
            }

            // Update Reference Assessment File Id
            if( $request->has('assessment_file_id') ) {
                $record->assessment_file_id = $request->get('assessment_file_id');
            }

            // Save Reference Attendance File Id
            if( $request->has('attendance_file_id') ) {
                $record->attendance_file_id = $request->get('attendance_file_id');
            }

            // Save Assessment Records ShortURL
            if( $request->has('assessment_short_title') ) {
                $record->assessment_short_title = $request->get('assessment_short_title');
            }

            // Save Application fees
            $request->has('application_fees') ? $record->application_fees = CourseMain::APP_FEES_TRUE : $record->application_fees = CourseMain::APP_FEES_FALSE;

            // save absorb gst
            $request->has('is_absorb_gst') ? $record->is_absorb_gst = CourseMain::ABSORB_GST_TRUE  : $record->is_absorb_gst = CourseMain::ABSORB_GST_FALSE;

            // save discount amount
            $request->has('is_discount') ? $record->is_discount = CourseMain::IS_DISCOUNT_TRUE : $record->is_discount = CourseMain::IS_DISCOUNT_FALSE;
            if(!empty($request->get('discount_amount'))){
                $record->discount_amount = $request->get('discount_amount');
            }
            $record->save();
            if($request->has('program_type_id') && !empty($request->get('program_type_id'))) {
                $record->programTypes()->sync($request->get('program_type_id')); // add user
            } else {
                $record->programTypes()->detach();
            }

            /*if($request->has('program_type_id') && !empty($request->get('program_type_id')[0])) {
                $record->programTypes()->sync($request->get('program_type_id')[0]); // add user
            } else {
                $record->programTypes()->detach();
            }*/


            /* Save Course Assessment Start */

            $courseId = $record->id;
            CourseAssessments::where('course_id', $courseId)->delete();
            if( $request->has('assessments') ) {
                $assessmentsCount = count($request->get('assessments'));
                if( $assessmentsCount > 0 ) {
                    for ($i = 0; $i < $assessmentsCount; $i++) {
                        
                        // add to assessment
                        $assessments = new CourseAssessments;
                        $assessments->course_id             = $courseId;
                        $assessments->assessment_file_title    = $request->get('assessments')[$i]['assessment_file_title'];
                        $assessments->assessment_file_id    = $request->get('assessments')[$i]['assessment_file_id'];
                        $assessments->start_time            = $request->get('assessments')[$i]['start_time'];
                        $assessments->end_time              = $request->get('assessments')[$i]['end_time'];
                        $assessments->short_url             = $request->get('assessments')[$i]['short_url'];
                        $assessments->created_by            = Auth::Id();
                        $assessments->updated_by            = Auth::Id();
                        $assessments->save();
                    }
                }
            }

        /* Save Course Assessment End */

            // XeroCourseLineItems::where('course_main_id', $record->id)->delete();
            // if( $request->has('items') ) {
            //     $items = $request->get('items');
            //     foreach( $items as $item ) {
            //         $recordItem = new XeroCourseLineItems;
            //         $recordItem->course_main_id = $record->id;
            //         foreach( $xeroitems as $xitem ) {
            //             if( $xitem['code'] == $item ) {
            //                 $recordItem->code = $xitem['code'];
            //                 $recordItem->name = $xitem['name'];
            //                 $recordItem->description = $xitem['description'];
            //                 $recordItem->amount = $xitem['sales_details']['unit_price'];
            //                 $recordItem->account_code = $xitem['sales_details']['account_code'];
            //                 break;
            //             }
            //         }
            //         $recordItem->save();
            //     }
            // }

            $record->trainers()->detach();
            $record->trainers()->attach($request->get('coursetrainers'));
            $record->courseTags()->sync($request->get('coursetag'));

            return $record;
        }
        return false;
    }

    public function searchCourseMainAjax($q)
    {
        $courseMains = $this->courseMain_model->where('name', 'like', '%'.$q.'%')
                            ->orWhere('reference_number', 'like', '%'.$q.'%')
                            ->where('course_type_id', CourseMain::SINGLE_COURSE)
                            ->limit(7)->get();
        $ret = [];
        foreach ($courseMains as $course) {
            if( $course->course_type_id == CourseMain::SINGLE_COURSE ) {
                $ret[] = [
                    "id"    => $course->id,
                    "text"  => $course->name.", ".$course->reference_number,
                ];
            }
        }
        return $ret;
    }

    public function getCourseMainByIds($courseIds)
    {
        $courseMains = $this->courseMain_model->whereIn('id', $courseIds)->get();
        $ret = [];
        foreach ($courseMains as $course) {
            $ret[] = [
                "id"    => $course->id,
                "text"  => $course->name.", ".$course->reference_number,
            ];
        }
        return $ret;
    }

}
