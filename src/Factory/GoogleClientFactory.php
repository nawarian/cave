<?php

declare(strict_types=1);

namespace App\Factory;

use App\Service\ProfileService;
use Google\Service\SearchConsole;
use Google_Client;

class GoogleClientFactory
{
    private ProfileService $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function createGoogleClient(): Google_Client
    {
        $profile = $this->profileService->getCurrentProfile();

        $client = new Google_Client();

        $client->addScope(SearchConsole::WEBMASTERS_READONLY);
        $client->setAccessType('offline');

        $client->setClientId($profile->getValue('gsc.client_id'));
        $client->setClientSecret($profile->getValue('gsc.client_secret'));
        $accessToken = $client->fetchAccessTokenWithRefreshToken($profile->getValue('gsc.refresh_token'));
        $client->setAccessToken($accessToken);

        return $client;
    }
}
