<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TaskAnnotationRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TaskAnnotationRepository::class)
 */
class TaskAnnotation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $text;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private ?DateTimeInterface $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Task::class, inversedBy="annotations")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Task $task;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): self
    {
        $this->task = $task;

        return $this;
    }
}
