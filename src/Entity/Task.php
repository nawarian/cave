<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TaskRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
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
    private string $summary;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTimeInterface $due;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $project;

    /**
     * @ORM\OneToMany(targetEntity=TaskLog::class, mappedBy="task", orphanRemoval=true)
     */
    private Collection $logs;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $status;

    /**
     * @ORM\ManyToOne(targetEntity=Profile::class, inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     */
    private Profile $profile;

    /**
     * @ORM\OneToMany(targetEntity=TaskAnnotation::class, mappedBy="task", orphanRemoval=true)
     */
    private Collection $annotations;

    public function __construct()
    {
        $this->annotations = new ArrayCollection();
        $this->logs = new ArrayCollection();
        $this->markAsPending();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getDue(): ?\DateTimeInterface
    {
        return $this->due;
    }

    public function setDue(\DateTimeInterface $due): self
    {
        $this->due = $due;

        return $this;
    }

    public function getProject(): ?string
    {
        return $this->project;
    }

    public function setProject(?string $project): self
    {
        $this->project = $project;

        return $this;
    }

    /**
     * @return Collection<int, TaskLog>
     */
    public function getLogs(): Collection
    {
        return $this->logs;
    }

    public function addLog(TaskLog $log): self
    {
        if (!$this->logs->contains($log)) {
            $this->logs[] = $log;
            $log->setTask($this);
        }

        return $this;
    }

    public function removeLog(TaskLog $log): self
    {
        if ($this->logs->removeElement($log)) {
            // set the owning side to null (unless already changed)
            if ($log->getTask() === $this) {
                $log->setTask(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function markAsPending(): void
    {
        $this->status = 'pending';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function markAsInProgress(): void
    {
        $this->status = 'progress';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'progress';
    }

    public function markAsDone(): void
    {
        $this->status = 'done';
    }

    public function isDone(): bool
    {
        return $this->status === 'done';
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * @return Collection<int, TaskAnnotation>
     */
    public function getAnnotations(): Collection
    {
        return $this->annotations;
    }

    public function addAnnotation(TaskAnnotation $annotation): self
    {
        if (!$this->annotations->contains($annotation)) {
            $this->annotations[] = $annotation;
            $annotation->setTask($this);
        }

        return $this;
    }

    public function removeAnnotation(TaskAnnotation $annotation): self
    {
        if ($this->annotations->removeElement($annotation)) {
            // set the owning side to null (unless already changed)
            if ($annotation->getTask() === $this) {
                $annotation->setTask(null);
            }
        }

        return $this;
    }
}
