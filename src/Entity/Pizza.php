<?php

namespace App\Entity;

use App\Repository\PizzaRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity(repositoryClass=PizzaRepository::class)
 */
class Pizza
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
    private $Name;

    /**
     * Many Pizzas has many Ingridients 
     * @ORM\ManyToMany(targetEntity="Ingridient", inversedBy="pizzas")
     * @ORM\JoinTable(name="pizzas_ingridients") 
     */
    private $ingridients;

    public function __construct()
    {
        $this->ingridients = new ArrayCollection();
    }

    public function getIngridients(): Collection
    {
        return $this->ingridients;
    }

    public function setIngridient(?Ingridient $ingridient): self
    {
        $this->ingridients->add($ingridient);

        return $this;
    }

    public function unsetIngridient(?Ingridient $ingridient): self
    {
        if ($this->ingridients->contains($ingridient)) {
            $this->ingridients->removeElement($ingridient);
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }
}
