<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoriesRepository")
 */
class Categories
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Events", inversedBy="categories")
     */
    private $belonging;

    public function __construct()
    {
        $this->belonging = new ArrayCollection();
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

    /**
     * @return Collection|Events[]
     */
    public function getBelonging(): Collection
    {
        return $this->belonging;
    }

    public function addBelonging(Events $belonging): self
    {
        if (!$this->belonging->contains($belonging)) {
            $this->belonging[] = $belonging;
        }

        return $this;
    }

    public function removeBelonging(Events $belonging): self
    {
        if ($this->belonging->contains($belonging)) {
            $this->belonging->removeElement($belonging);
        }

        return $this;
    }

}
