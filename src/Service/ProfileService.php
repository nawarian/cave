<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Profile;
use App\Repository\ProfileRepository;
use Doctrine\ORM\EntityNotFoundException;

class ProfileService
{
    private ProfileRepository $profileRepository;

    public function __construct(ProfileRepository $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    public function getCurrentProfile(): Profile
    {
        $profile = $this->profileRepository->findOneBy(['current' => true]);

        if ($profile === null) {
            throw new EntityNotFoundException("Could not find an active profile.");
        }

        return $profile;
    }
}
