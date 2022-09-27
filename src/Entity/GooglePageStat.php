<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\GooglePageStatRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GooglePageStatRepository::class)
 */
class GooglePageStat
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
    private string $page;

    /**
     * @ORM\Column(type="integer")
     */
    private int $clicks;

    /**
     * @ORM\Column(type="integer")
     */
    private int $impressions;

    /**
     * @ORM\Column(type="float")
     */
    private float $ctr;

    /**
     * @ORM\Column(type="float")
     */
    private float $position;

    /**
     * @ORM\Column(type="date")
     */
    private DateTimeInterface $date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $site;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPage(): ?string
    {
        return $this->page;
    }

    public function setPage(string $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function getClicks(): ?int
    {
        return $this->clicks;
    }

    public function setClicks(int $clicks): self
    {
        $this->clicks = $clicks;

        return $this;
    }

    public function getImpressions(): ?int
    {
        return $this->impressions;
    }

    public function setImpressions(int $impressions): self
    {
        $this->impressions = $impressions;

        return $this;
    }

    public function getCtr(): ?float
    {
        return $this->ctr;
    }

    public function setCtr(float $ctr): self
    {
        $this->ctr = $ctr;

        return $this;
    }

    public function getPosition(): ?float
    {
        return $this->position;
    }

    public function setPosition(float $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getSite(): ?string
    {
        return $this->site;
    }

    public function setSite(string $site): self
    {
        $this->site = $site;

        return $this;
    }
}
