<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TweetMediaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TweetMediaRepository::class)
 */
class TweetMedia
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Tweet::class, inversedBy="medias")
     * @ORM\JoinColumn(nullable=false)
     */
    private Tweet $tweet;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $uploaded = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $twitterMediaId = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $altText;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $filepath;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTweet(): ?Tweet
    {
        return $this->tweet;
    }

    public function setTweet(?Tweet $tweet): self
    {
        $this->tweet = $tweet;

        return $this;
    }

    public function isUploaded(): ?bool
    {
        return $this->uploaded;
    }

    public function setUploaded(bool $uploaded): self
    {
        $this->uploaded = $uploaded;

        return $this;
    }

    public function getTwitterMediaId(): ?string
    {
        return $this->twitterMediaId;
    }

    public function setTwitterMediaId(string $twitterMediaId): self
    {
        $this->twitterMediaId = $twitterMediaId;

        return $this;
    }

    public function getAltText(): ?string
    {
        return $this->altText;
    }

    public function setAltText(string $altText): self
    {
        $this->altText = $altText;

        return $this;
    }

    public function getFilepath(): ?string
    {
        return $this->filepath;
    }

    public function setFilepath(string $filepath): self
    {
        $this->filepath = $filepath;

        return $this;
    }
}
