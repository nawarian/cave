<?php

declare(strict_types=1);

namespace App\Service;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Entity\Profile;
use RuntimeException;
use SplFileInfo;

class TwitterService
{
    private ProfileService $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    /** @return array{ id: string, name: string, handle: string } */
    public function whoAmI(): array
    {
        $res = $this->client()->get('account/verify_credentials');

        return [
            'id' => $res->id_str,
            'name' => $res->name,
            'handle' => $res->screen_name,
        ];
    }

    public function uploadMedia(SplFileInfo $fileInfo): string
    {
        $res = $this->client()->upload('media/upload', [
            'media' => $fileInfo->getRealPath(),
        ]);

        return (string) $res->media_id_string;
    }

    public function tweet(string $text, array $mediaIds): string
    {
        $res = $this->client()->post('statuses/update', [
            'status' => $text,
            'media_ids' => $mediaIds,
        ]);

        $statusId = $res->id_str;
        $username = $res->user->screen_name;

        return "https://twitter.com/{$username}/status/{$statusId}";
    }

    private function client(): TwitterOAuth
    {
        $profile = $this->profileService->getCurrentProfile();

        $consumerKey = $this->getProfileConfig($profile, 'twitter.consumer_key');
        $consumerSecret = $this->getProfileConfig($profile, 'twitter.consumer_secret');
        $oauthToken = $this->getProfileConfig($profile, 'twitter.access_token');
        $oauthTokenSecret = $this->getProfileConfig($profile, 'twitter.access_token_secret');

        return new TwitterOAuth($consumerKey, $consumerSecret, $oauthToken, $oauthTokenSecret);
    }

    private function getProfileConfig(Profile $profile, string $key): string
    {
        $value = $profile->getValue($key);

        if ($value === null) {
            throw new RuntimeException(
                "The profile key '{$key}' was not found in profile '{$profile->getName()}'."
            );
        }

        return $value;
    }
}
