<?php

namespace App\Entity;

use App\Repository\IngridientRepository;
use Doctrine\ORM\Mapping as ORM;
use Laminas\Code\Reflection\FunctionReflection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass=IngridientRepository::class)
 */
class Ingridient
{

    /**
     * Many Ingridients have Many Pizzas.
     * @ORM\ManyToMany(targetEntity="Pizza", mappedBy="ingridients")
     */
    private $pizzas;

    public function __construct()
    {
        $this->pizzas = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * @ORM\Column(type="float")
     */
    private $Price;

    public function getPizzas(): Collection
    {
        return $this->pizzas;
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

    public function getPrice(): ?float
    {
        return $this->Price;
    }

    public function setPrice(float $Price): self
    {
        $this->Price = $Price;

        return $this;
    }
}
