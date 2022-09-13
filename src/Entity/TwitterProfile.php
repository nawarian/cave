<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TwitterProfileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TwitterProfileRepository::class)
 */
class TwitterProfile
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $handle;

    /**
     * @ORM\Column(type="integer")
     */
    private int $followers;

    /**
     * @ORM\Column(type="integer")
     */
    private int $friends;

    /**
     * @ORM\OneToMany(targetEntity=TwitterProfileStat::class, mappedBy="profile", orphanRemoval=true)
     */
    private Collection $stats;

    public function __construct()
    {
        $this->stats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHandle(): ?string
    {
        return $this->handle;
    }

    public function setHandle(string $handle): self
    {
        $this->handle = $handle;

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

    /**
     * @return Collection<int, TwitterProfileStat>
     */
    public function getStats(): Collection
    {
        return $this->stats;
    }

    public function addStat(TwitterProfileStat $stat): self
    {
        if (!$this->stats->contains($stat)) {
            $this->stats[] = $stat;
            $stat->setProfile($this);
        }

        return $this;
    }

    public function removeStat(TwitterProfileStat $stat): self
    {
        if ($this->stats->removeElement($stat)) {
            // set the owning side to null (unless already changed)
            if ($stat->getProfile() === $this) {
                $stat->setProfile(null);
            }
        }

        return $this;
    }
}
