<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProfileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProfileRepository::class)
 */
class Profile
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=ProfileKV::class, mappedBy="profile", orphanRemoval=true)
     */
    private $kv;

    /**
     * @ORM\Column(type="boolean")
     */
    private $current;

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="profile", orphanRemoval=true)
     */
    private $tasks;

    public function __construct()
    {
        $this->kv = new ArrayCollection();
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(string $key): ?string
    {
        /** @var ProfileKV|false $profileKV */
        $profileKV = $this->getKv()->filter(fn(ProfileKV $kv) => $kv->getKey() === $key)->first();
        if (!$profileKV) {
            return null;
        }

        return $profileKV->getValue();
    }

    /**
     * @return Collection<int, ProfileKV>
     */
    public function getKv(): Collection
    {
        return $this->kv;
    }

    public function addKv(ProfileKV $kv): self
    {
        if (!$this->kv->contains($kv)) {
            $this->kv[] = $kv;
            $kv->setProfile($this);
        }

        return $this;
    }

    public function removeKv(ProfileKV $kv): self
    {
        if ($this->kv->removeElement($kv)) {
            // set the owning side to null (unless already changed)
            if ($kv->getProfile() === $this) {
                $kv->setProfile(null);
            }
        }

        return $this;
    }

    public function isCurrent(): ?bool
    {
        return $this->current;
    }

    public function setCurrent(bool $current): self
    {
        $this->current = $current;

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setProfile($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getProfile() === $this) {
                $task->setProfile(null);
            }
        }

        return $this;
    }
}
