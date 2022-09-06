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

    public function __construct()
    {
        $this->logs = new ArrayCollection();
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
}
