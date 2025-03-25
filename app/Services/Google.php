<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class Google
{
    public function client()
    {
        $client = new \Google_Client();
        $client->setClientId(config('google.client_id'));
        $client->setClientSecret(config('google.client_secret'));
        $client->setRedirectUri(config('google.redirect_uri'));
        $client->setScopes(explode(',', config('google.feedback_scopes')));
        $client->setApprovalPrompt(config('google.feedback_approval_prompt'));
        $client->setAccessType(config('google.access_type'));
        return $client;
    }

    public function doc($client)
    {
        $doc = new \Google\Service\Docs($client);
        return $doc;
    }
    public function drive($client)
    {
        $drive = new \Google\Service\Drive($client);
        return $drive;
    }
    public function service($client)
    {
        $service = new \Google\Service\Books($client);
        return $service;
    }
}
