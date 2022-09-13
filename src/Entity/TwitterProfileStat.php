<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TwitterProfileStatRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TwitterProfileStatRepository::class)
 */
class TwitterProfileStat
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=TwitterProfile::class, inversedBy="stats")
     * @ORM\JoinColumn(nullable=false)
     */
    private TwitterProfile $profile;

    /**
     * @ORM\Column(type="integer")
     */
    private int $followers;

    /**
     * @ORM\Column(type="integer")
     */
    private int $friends;

    /**
     * @ORM\Column(type="date")
     */
    private DateTimeInterface $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProfile(): ?TwitterProfile
    {
        return $this->profile;
    }

    public function setProfile(?TwitterProfile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    public function getFollowers(): ?int
    {
        return $this->followers;
    }

    public function setFollowers(int $followers): self
    {
        $this->followers = $followers;

        return $this;
    }

    public function getFriends(): ?int
    {
        return $this->friends;
    }

    public function setFriends(int $friends): self
    {
        $this->friends = $friends;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
