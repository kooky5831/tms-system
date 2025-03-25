<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ActiveCampaignService;

class ActiveCampaignController extends Controller
{
    protected $activeCampaignController;
    
    public function __construct() {
        $this->activeCampaignService = new ActiveCampaignService;
    }
    
    //Active Campaign Tags functions
    public function getTagFromActiveCampaings(){
        $response = $this->activeCampaignService->getMethodOfActiveCampaign('tags');
    }

    public function addTagToActiveCampaings(){
        
        $data = [
            "tag" => [  
                "tag" => "TEST EASG Completed : DME", 
                "tagType" => "contact",
                "description" => "This is testing tag for added digital marketing assessntial course completed"
            ]
        ];
        $response = $this->activeCampaignService->getMethodOfActiveCampaign('tags', $data);
    }

    public function setTagToContactOnActiveCampaings(){
        $data = [
            "contactTag" => [  
                "contact" => "62653", 
                "tag" => "217",
            ]
        ];
        $response = $this->activeCampaignService->getMethodOfActiveCampaign('contactTags', $data);
    }

    //Active Campaign Contacts functions
    public function getContactFromActiveCampaingsByEmail($enrolmentEmail){
        $response = $this->activeCampaignService->getMethodOfActiveCampaign("contacts?filters[email]=", $enrolmentEmail);
    }

    public function addContactOnActiveCampaings(){
        $data = [
            "contact" => [  
                "email" => "testjohndoe@example.com", 
                "firstName" => "testjohndoe",
                "lastName"  => "Doe",
                "phone" => "7223224241"
            ]
        ];
        $response = $this->activeCampaignService->getMethodOfActiveCampaign('contacts', $data);
    }

    public function getListFromActiveCampaings() {
        $response = $this->activeCampaignService->getListOfActiveCampaign('lists');
    }

    public function getContactList($data) {
        
    }
}
