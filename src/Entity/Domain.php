<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DomainRepository")
 */
class Domain
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $domain;


    /**
     * @ORM\Column(type="string", name="users")
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="domains")
     */
    private $domainUsers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Project", mappedBy="domain")
     */
    private $projects;


    public function __construct()
    {
        $this->domainUsers = new ArrayCollection();
        $this->projects = new ArrayCollection();
    }

    public function __toString()
    {
        return (string) $this->getDomainUsers();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }


    public function getUsers(): ?int
    {
        return $this->users;
    }

    public function setUsers(int $users): self
    {
        $this->users = $users;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getDomainUsers(): Collection
    {
        return $this->domainusers;
    }

    public function addDomainUser(User $domainUser): self
    {
        if (!$this->domainusers->contains($domainUser)) {
            $this->domainusers[] = $domainUser;
        }

        return $this;
    }

    public function removeDomainUser(User $domainUser): self
    {
        if ($this->domainusers->contains($domainUser)) {
            $this->domainusers->removeElement($domainUser);
        }

        return $this;
    }

    /**
     * @return Collection|Project[]
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->setDomain($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
            // set the owning side to null (unless already changed)
            if ($project->getDomain() === $this) {
                $project->setDomain(null);
            }
        }

        return $this;
    }


}