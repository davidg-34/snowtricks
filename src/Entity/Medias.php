<?php

namespace App\Entity;

use App\Repository\MediasRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MediasRepository::class)]
class Medias
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
    private ?string $picture = null;

    #[ORM\Column(length: 120)]
    private ?string $video = null;

    #[ORM\OneToMany(mappedBy: 'medias', targetEntity: Tricks::class)]
    private Collection $relation;

    public function __construct()
    {
        $this->relation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(string $video): self
    {
        $this->video = $video;

        return $this;
    }

    /**
     * @return Collection<int, Tricks>
     */
    public function getRelation(): Collection
    {
        return $this->relation;
    }

    public function addRelation(Tricks $relation): self
    {
        if (!$this->relation->contains($relation)) {
            $this->relation->add($relation);
            $relation->setMedias($this);
        }

        return $this;
    }

    public function removeRelation(Tricks $relation): self
    {
        if ($this->relation->removeElement($relation)) {
            // set the owning side to null (unless already changed)
            if ($relation->getMedias() === $this) {
                $relation->setMedias(null);
            }
        }

        return $this;
    }
}
