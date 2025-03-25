<?php

namespace App\Console\Commands;

use App\Models\Course;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use App\Models\StudentEnrolment;
use App\Services\ActiveCampaignService;

class GetCompletedCourserunEnrolments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:get-completed-courserun-enrolments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is get completed course enrolments and add tag to active campaing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $activeCampaignService = new ActiveCampaignService;
        $previousDate = Carbon::now()->subDays(1)->format('Y-m-d');
        $getCourseRuns = Course::where('course_end_date', $previousDate)->with(['courseActiveCampaing', 'courseMain'])->get();

        foreach($getCourseRuns as $courseRun){
            $studentEnrolments = $courseRun->courseActiveCampaing;
            foreach($studentEnrolments as $enrol){
                if($enrol->assessment == 'c'){
                    $email = $enrol->email;
                    $courseCode = $courseRun->courseMain->course_abbreviation != null ? $courseRun->courseMain->course_abbreviation : "EA-TMS";
                    $tagWithPreFix = "PTM - ".$courseCode;
                    // Check Tag
                    $checkTagIsAvailable = $activeCampaignService->checkTagOnActiveCampaign("tags?filters[tag]=", $tagWithPreFix);
                    if($checkTagIsAvailable['response']){
                        // Tag is true
                        $tagId = $checkTagIsAvailable['data']['tags'][0]['id'];
                        //Check contact
                        $checkContactIsAvailable = $activeCampaignService->checkContactOnActiveCampaign("contacts?filters[email]=", $email);
                        if($checkContactIsAvailable['response']){
                            //Contact is true
                            $contactId = $checkContactIsAvailable['data']['contacts'][0]['id'];
                            $data = ["contactTag" => ["contact" => $contactId, "tag" => $tagId]];
                            $addedTagOnContact = $activeCampaignService->setTagOnContact('contactTags', $data);
                            if(isset($addedTagOnContact['data']['contacts']) && $addedTagOnContact['response']) {
                                \Log::info("Tag added condition one");
                                // return "Tag added";
                            } elseif($addedTagOnContact['data']['contactTag'] && $addedTagOnContact['response']) {
                                \Log::info("Tag already added condition one");
                                // return "Tag already added";
                            } else {
                                // return $addedTagOnContact['message'];
                            }

                            $checkListOfContact = $activeCampaignService->checkListOfContactOnActiveCampaign('contacts/'.$contactId.'/contactLists');
                            if($checkListOfContact['response']){
                                $getListIds = $activeCampaignService->getListIdArrays($checkListOfContact['data']['contactLists']);
                                if(!in_array(1, $getListIds)){
                                    $data = ['contactList' => ['list' => 1, 'contact' => $contactId, 'status' => 1]];
                                    $setContactData = $activeCampaignService->syncContactToListOnActiveCampaign('contactLists', $data);
                                    if($setContactData['response']){
                                        \Log::info('Contact is synced with list');
                                    } else {
                                        \Log::info('Something went wrong with sync contact list');
                                    }
                                }
                            } else {
                                $data = ['contactList' => ['list' => 1, 'contact' => $contactId, 'status' => 1]];
                                $setContactData = $activeCampaignService->syncContactToListOnActiveCampaign('contactLists', $data);
                                if($setContactData['response']){
                                    \Log::info('Contact is synced with list');
                                } else {
                                    \Log::info('Something went wrong with sync contact list');
                                }
                            }

                        } else {
                            //Contact is false
                            $studentName = $enrol->student->name;
                            $studentMobileNo = $enrol->student->mobile_no;
                            $data = [ "contact" => [ "email" => $email, "firstName" => $studentName, "lastName"  => "", "phone" => $studentMobileNo]];
                            $addContact = $activeCampaignService->addContactOnActiveCampaign('contacts', $data);
                            if($addContact['response']) {
                                $contactId = $addContact['data']['contact']['id'];
                                $data = ["contactTag" => ["contact" => $contactId, "tag" => $tagId]];
                                $addedTagOnContact = $activeCampaignService->setTagOnContact('contactTags', $data);
                                if(isset($addedTagOnContact['data']['contacts']) && $addedTagOnContact['response']) {
                                    \Log::info("Tag added condition two");
                                    // return "Tag added";
                                } elseif($addedTagOnContact['data']['contactTag'] && $addedTagOnContact['response']) {
                                    \Log::info("Tag already added condition two");
                                    // return "Tag already added";
                                } else {
                                    // return $addedTagOnContact['message'];
                                }

                                $checkListOfContact = $activeCampaignService->checkListOfContactOnActiveCampaign('contacts/'.$contactId.'/contactLists');
                                if($checkListOfContact['response']){
                                    $getListIds = $activeCampaignService->getListIdArrays($checkListOfContact['data']['contactLists']);
                                    if(!in_array(1, $getListIds)){
                                        $data = ['contactList' => ['list' => 1, 'contact' => $contactId, 'status' => 1]];
                                        $setContactData = $activeCampaignService->syncContactToListOnActiveCampaign('contactLists', $data);
                                        if($setContactData['response']){
                                            \Log::info('Contact is synced with list');
                                        } else {
                                            \Log::info('Something went wrong with sync contact list');
                                        }
                                    }
                                } else {
                                    $data = ['contactList' => ['list' => 1, 'contact' => $contactId, 'status' => 1]];
                                    $setContactData = $activeCampaignService->syncContactToListOnActiveCampaign('contactLists', $data);
                                    if($setContactData['response']){
                                        \Log::info('Contact is synced with list');
                                    } else {
                                        \Log::info('Something went wrong with sync contact list');
                                    }
                                }

                            } else {
                                // return $addContact['message'];
                            }
                        }
                    } else {
                        // Tag is false
                        $tagType = "contact";
                        $tag = $tagWithPreFix;
                        $description = "This tag is used for completed " . $courseRun->courseMain->name . " course (TMS)";
                        $data = [ "tag" => [ "tag" => $tag, "tagType" => $tagType, "description" => $description ]];
                        $addedTag = $activeCampaignService->addTagOnActiveCampaign('tags', $data);
                        if($addedTag['response']){
                            $tagId = $addedTag['data']['tag']['id'];
                            $checkContactIsAvailable = $activeCampaignService->checkContactOnActiveCampaign("contacts?filters[email]=", $email);

                            if($checkContactIsAvailable['response']){
                                //Contact is true
                                $contactId = $checkContactIsAvailable['data']['contacts'][0]['id'];
                                $data = ["contactTag" => ["contact" => $contactId, "tag" => $tagId]];
                                $addedTagOnContact = $activeCampaignService->setTagOnContact('contactTags', $data);
                                if(isset($addedTagOnContact['data']['contacts']) && $addedTagOnContact['response']) {
                                    \Log::info("Tag added condition three");
                                    // return "Tag added";
                                } elseif($addedTagOnContact['data']['contactTag'] && $addedTagOnContact['response']) {
                                    \Log::info("Tag already added condition three");
                                    // return "Tag already added";
                                } else {
                                    \Log::info("Something went wrong on tag addition");
                                    // return $addedTagOnContact['message'];
                                }

                                $checkListOfContact = $activeCampaignService->checkListOfContactOnActiveCampaign('contacts/'.$contactId.'/contactLists');
                                if($checkListOfContact['response']){
                                    $getListIds = $activeCampaignService->getListIdArrays($checkListOfContact['data']['contactLists']);
                                    if(!in_array(1, $getListIds)){
                                        $data = ['contactList' => ['list' => 1, 'contact' => $contactId, 'status' => 1]];
                                        $setContactData = $activeCampaignService->syncContactToListOnActiveCampaign('contactLists', $data);
                                        if($setContactData['response']){
                                            \Log::info('Contact is synced with list');
                                        } else {
                                            \Log::info('Something went wrong with sync contact list');
                                        }
                                    }
                                } else {
                                    $data = ['contactList' => ['list' => 1, 'contact' => $contactId, 'status' => 1]];
                                    $setContactData = $activeCampaignService->syncContactToListOnActiveCampaign('contactLists', $data);
                                    if($setContactData['response']){
                                        \Log::info('Contact is synced with list');
                                    } else {
                                        \Log::info('Something went wrong with sync contact list');
                                    }
                                }

                            } else {
                                //Contact is false
                                $studentName = $enrol->student->name;
                                $studentMobileNo = $enrol->student->mobile_no;
                                $data = [ "contact" => [ "email" => $email, "firstName" => $studentName, "lastName"  => "", "phone" => $studentMobileNo]];
                                $addContact = $activeCampaignService->addContactOnActiveCampaign('contacts', $data);
                                if($addContact['response']){
                                    $contactId = $addContact['data']['contact']['id'];
                                    $data = ["contactTag" => ["contact" => $contactId, "tag" => $tagId]];
                                    $addedTagOnContact = $activeCampaignService->setTagOnContact('contactTags', $data);
                                    if(isset($addedTagOnContact['data']['contacts']) && $addedTagOnContact['response']) {
                                        \Log::info("Tag added condition four");
                                        // return "Tag added";
                                    } elseif($addedTagOnContact['data']['contactTag'] && $addedTagOnContact['response']) {
                                        \Log::info("Tag already added condition four");
                                        // return "Tag already added";
                                    } else {
                                        \Log::info("Something went wrong on course");
                                        // return $addedTagOnContact['message'];
                                    }

                                    $checkListOfContact = $activeCampaignService->checkListOfContactOnActiveCampaign('contacts/'.$contactId.'/contactLists');
                                    if($checkListOfContact['response']){
                                        $getListIds = $activeCampaignService->getListIdArrays($checkListOfContact['data']['contactLists']);
                                        if(!in_array(1, $getListIds)){
                                            $data = ['contactList' => ['list' => 1, 'contact' => $contactId, 'status' => 1]];
                                            $setContactData = $activeCampaignService->syncContactToListOnActiveCampaign('contactLists', $data);
                                            if($setContactData['response']){
                                                \Log::info('Contact is synced with list');
                                            } else {
                                                \Log::info('Something went wrong with sync contact list');
                                            }
                                        }
                                    } else {
                                        $data = ['contactList' => ['list' => 1, 'contact' => $contactId, 'status' => 1]];
                                        $setContactData = $activeCampaignService->syncContactToListOnActiveCampaign('contactLists', $data);
                                        if($setContactData['response']){
                                            \Log::info('Contact is synced with list');
                                        } else {
                                            \Log::info('Something went wrong with sync contact list');
                                        }
                                    }
                                    
                                } else {
                                    // return $addContact['message'];
                                }
                            }
                        } else {
                            \Log::info("somthing went wrong in tag creation check logs");
                            // return "somthing went wrong in tag creation check logs";
                        }
                    }
                }
            }
        }
    }
}