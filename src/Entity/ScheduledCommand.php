<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ScheduledCommandRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ScheduledCommandRepository::class)
 */
class ScheduledCommand
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeInterface $due;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    private string $commandLine;

    /**
     * @ORM\Column(type="integer")
     */
    private int $attempts;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDue(): ?DateTimeInterface
    {
        return $this->due;
    }

    public function setDue(DateTimeInterface $due): self
    {
        $this->due = $due;

        return $this;
    }

    public function getCommandLine(): ?string
    {
        return $this->commandLine;
    }

    public function setCommandLine(string $commandLine): self
    {
        $this->commandLine = $commandLine;

        return $this;
    }

    public function getAttempts(): ?int
    {
        return $this->attempts;
    }

    public function setAttempts(int $attempts): self
    {
        $this->attempts = $attempts;

        return $this;
    }
}
