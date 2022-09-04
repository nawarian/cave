<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TweetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TweetRepository::class)
 */
class Tweet
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
    private string $text;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $replyToTweetId = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $mention = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $sent = false;

    /**
     * @ORM\OneToMany(targetEntity=TweetMedia::class, mappedBy="tweet", orphanRemoval=true, cascade={"persist"})
     */
    private Collection $medias;

    /**
     * @ORM\ManyToOne(targetEntity=TwitterThread::class, inversedBy="tweets")
     */
    private ?TwitterThread $twitterThread = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $twitterTweetId = null;

    public function __construct()
    {
        $this->medias = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getReplyToTweetId(): ?string
    {
        return $this->replyToTweetId;
    }

    public function setReplyToTweetId(?string $replyToTweetId): self
    {
        $this->replyToTweetId = $replyToTweetId;

        return $this;
    }

    public function getMention(): ?string
    {
        return $this->mention;
    }

    public function setMention(?string $mention): self
    {
        $this->mention = $mention;

        return $this;
    }

    public function isSent(): bool
    {
        return $this->sent;
    }

    public function setSent(bool $sent): self
    {
        $this->sent = $sent;

        return $this;
    }

    /**
     * @return Collection<int, TweetMedia>
     */
    public function getMedias(): Collection
    {
        return $this->medias;
    }

    public function addMedia(TweetMedia $media): self
    {
        if (!$this->medias->contains($media)) {
            $this->medias[] = $media;
            $media->setTweet($this);
        }

        return $this;
    }

    public function removeMedia(TweetMedia $media): self
    {
        if ($this->medias->removeElement($media)) {
            // set the owning side to null (unless already changed)
            if ($media->getTweet() === $this) {
                $media->setTweet(null);
            }
        }

        return $this;
    }

    public function getTwitterThread(): ?TwitterThread
    {
        return $this->twitterThread;
    }

    public function setTwitterThread(?TwitterThread $twitterThread): self
    {
        $this->twitterThread = $twitterThread;

        return $this;
    }

    public function getTwitterTweetId(): ?string
    {
        return $this->twitterTweetId;
    }

    public function setTwitterTweetId(?string $twitterTweetId): self
    {
        $this->twitterTweetId = $twitterTweetId;

        return $this;
    }
}
