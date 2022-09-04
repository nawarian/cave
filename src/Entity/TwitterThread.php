<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TwitterThreadRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TwitterThreadRepository::class)
 */
class TwitterThread
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\OneToMany(targetEntity=Tweet::class, mappedBy="twitterThread")
     */
    private Collection $tweets;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $processed = false;

    public function __construct()
    {
        $this->tweets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Tweet>
     */
    public function getTweets(): Collection
    {
        return $this->tweets;
    }

    public function addTweet(Tweet $tweet): self
    {
        if (!$this->tweets->contains($tweet)) {
            $this->tweets[] = $tweet;
            $tweet->setTwitterThread($this);
        }

        return $this;
    }

    public function removeTweet(Tweet $tweet): self
    {
        if ($this->tweets->removeElement($tweet)) {
            // set the owning side to null (unless already changed)
            if ($tweet->getTwitterThread() === $this) {
                $tweet->setTwitterThread(null);
            }
        }

        return $this;
    }

    public function isProcessed(): ?bool
    {
        return $this->processed;
    }

    public function setProcessed(bool $processed): self
    {
        $this->processed = $processed;

        return $this;
    }
}
