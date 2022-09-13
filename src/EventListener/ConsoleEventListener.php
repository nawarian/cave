<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Profile;
use App\Repository\ProfileRepository;
use App\Service\ProfileService;
use RuntimeException;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConsoleEventListener implements EventSubscriberInterface
{
    private ?string $oldProfile = null;

    public ProfileService $profileService;
    public ProfileRepository $profileRepository;

    public function __construct(ProfileService $profileService, ProfileRepository $profileRepository)
    {
        $this->profileService = $profileService;
        $this->profileRepository = $profileRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'console.command' => 'onConsoleCommand',
            'console.terminate' => 'onConsoleTerminate',
        ];
    }

    public function onConsoleCommand(ConsoleCommandEvent $e): void
    {
        $overrideProfileName = $e->getInput()->getOption('profile');
        if ($overrideProfileName !== null) {
            $oldProfile = $this->profileService->getCurrentProfile();
            $this->oldProfile = $oldProfile->getName();

            $this->profileService->setCurrentProfile(
                $this->getProfileByName($overrideProfileName)
            );
        }
    }

    public function onConsoleTerminate(ConsoleTerminateEvent $_): void
    {
        if ($this->oldProfile !== null) {
            $this->profileService->setCurrentProfile(
                $this->getProfileByName($this->oldProfile)
            );
        }
    }

    private function getProfileByName(string $name): Profile
    {
        $profile = $this->profileRepository->findOneBy(['name' => $name]);

        if ($profile === null) {
            throw new RuntimeException("Could not find profile '{$name}'");
        }

        return $profile;
    }
}
