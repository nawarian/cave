<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TaskLogRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TaskLogRepository::class)
 */
class TaskLog
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Task::class, inversedBy="logs")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Task $task = null;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTimeInterface $start;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $finish = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStart(): ?DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getFinish(): ?DateTimeInterface
    {
        return $this->finish;
    }

    public function setFinish(?DateTimeInterface $finish): self
    {
        $this->finish = $finish;

        return $this;
    }
}
