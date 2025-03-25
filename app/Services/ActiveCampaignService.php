<?php

namespace App\Services;
use App\Models\StudentEnrolment;
use App\Models\Course;
use Illuminate\Support\Facades\Log;


class ActiveCampaignService
{
    private $student_enrolment_model;
    private $activeCampaignUrl;
    private $activeCampaignToken;
    private $activeCampaignUrlVersion;


    public function __construct(){
        $this->student_enrolment_model = new StudentEnrolment;
        $this->activeCampaignUrl = env('ACTIVE_CAMPAIGN_URL');
        $this->activeCampaignToken = env('ACTIVE_CAMPAIGN_TOKEN');
        $this->activeCampaignUrlVersion = env('ACTIVE_CAMPAIGN_URL_VERSION');
    }
    
    public function getMethodOfActiveCampaign($endpoints, $getData = null) {
        $getData = $getData != null ? urlencode($getData) : '';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL =>  $this->activeCampaignUrl . $this->activeCampaignUrlVersion . $endpoints . $getData,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            // CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Api-Token: '.$this->activeCampaignToken,
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function postMethodOfActiveCampaign($endpoints, $data) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->activeCampaignUrl . $this->activeCampaignUrlVersion . $endpoints,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            // CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Api-Token: '.$this->activeCampaignToken,
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function checkTagOnActiveCampaign($endpoint, $keyword) {
        $tagData = $this->getMethodOfActiveCampaign($endpoint, $keyword);
        \Log::info("Get Tag Data ===>> " . $tagData);
        $tagData = json_decode($tagData, true);
        if(empty($tagData['tags'])){
            return ['response' => false, 'message' => 'tag is not available need to create'];
        } else {
            return ['response' => true, 'data' => $tagData];
        }
    }

    public function addTagOnActiveCampaign($endpoint, $data) {
        $addTagData = $this->postMethodOfActiveCampaign($endpoint, $data);
        \Log::info("Add Tag Data ===>> " . $addTagData);
        $addTagData = json_decode($addTagData, true);
        if(empty($addTagData['tag'])){
            return ['response' => false, 'message' => 'Something went wrong please check API'];
        } else {
            return ['response' => true, 'data' => $addTagData];
        }
    }

    public function checkContactOnActiveCampaign($endpoint, $keyword) {
        $contactData = $this->getMethodOfActiveCampaign($endpoint, $keyword);
        \Log::info("Get Conatct Data ===>> " . $contactData);
        $contactData = json_decode($contactData, true);
        if(empty($contactData['contacts'])){
            return ['response' => false, 'message' => 'tag is not available need to create'];
        } else {
            return ['response' => true, 'data' => $contactData];
        }
    }

    public function addContactOnActiveCampaign($endpoint, $data){
        $addContactData = $this->postMethodOfActiveCampaign($endpoint, $data);
        \Log::info("Add Conatct Data ===>> " . $addContactData);
        $addContactData = json_decode($addContactData, true);
        if(isset($addContactData['errors'])){
            return ['response' => false, 'message' => $addContactData['errors'][0]['title']];
        } else {
            if(empty($addContactData['contact'])){
                return ['response' => false, 'message' => 'Something went wrong please check API'];
            } else {
                return ['response' => true, 'data' => $addContactData];
            }
        }
    }

    public function setTagOnContact($endpoint, $data){
        $addTagToContact = $this->postMethodOfActiveCampaign($endpoint, $data);
        \Log::info("Set Tag Data ===>> " . $addTagToContact);
        $addTagToContact = json_decode($addTagToContact, true);
        if(isset($addTagToContact['message'])){
            return ['response' => false, 'message' => $addTagToContact['message']];
        } else {
            return ['response' => true, 'data' => $addTagToContact];
        }
    }

    public function getListOfActiveCampaign($endpoint) {
        $getListsData = $this->getMethodOfActiveCampaign($endpoint);
        \Log::info("Get List Data ===>> " . $getListsData);
        $getListsData = json_decode($getListsData, true);
    }

    public function checkListOfContactOnActiveCampaign($endpoint) {
        $listData = $this->getMethodOfActiveCampaign($endpoint);
        \Log::info("Get Conatct's List Data ===>> " . $listData);
        $listData = json_decode($listData, true);
        if(empty($listData['contactLists'])){
            return ['response' => false, 'message' => 'list not found'];
        } else {
            return ['response' => true, 'data' => $listData];
        }
    }

    public function syncContactToListOnActiveCampaign($endpoint, $data) {
        $addContactToList = $this->postMethodOfActiveCampaign($endpoint, $data);
        \Log::info("Set Conatct on List ===>> " . $addContactToList);
        $addContactToList = json_decode($addContactToList, true);
        if(empty($addContactToList['contacts'])){
            return ['response' => false, 'message' => 'Something Went Wrong'];
        } else {
            return ['response' => true, 'data' => $addContactToList];
        }
    }

    public function getListIdArrays($data){
        $listIds = [];
        foreach($data as $key => $value){
            $listIds[] = $value['list'];
        }
        return $listIds;
    }
}