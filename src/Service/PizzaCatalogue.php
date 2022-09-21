<?php

namespace App\Service;

use App\Entity\Pizza;
use App\Entity\Ingridient;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\Migrations\Tools\Console\Exception\VersionDoesNotExist;
use Doctrine\Persistence\ManagerRegistry;
use App\Dto\PizzaFullInfo;

class PizzaCatalogue
{

    private $serializer;
    private $managerRegistry;

    public function __construct(SerializerInterface $serializer, ManagerRegistry $managerRegistry)
    {
        $this->serializer = $serializer;
        $this->managerRegistry = $managerRegistry;
    }

    public function getPizzaFullInfo(Pizza $pizza): array
    {
        $ingridientPriceCost = 0;

        $ingridients = $this->getPizzaIngridients($pizza);

        foreach ($ingridients as $ingridient) {
            $ingridientPriceCost += $ingridient->getPrice();
        }

        return [
            'id' => $pizza->getId(),
            'name' => $pizza->getName(),
            'price' => $ingridientPriceCost + ($ingridientPriceCost * 0.5)
        ];
    }

    public function getPizzaIngridients(Pizza $pizza): array
    {
        return $pizza->getIngridients()->toArray();
    }

    public function appendIngridientById(Pizza $pizza, int $ingridientId): void
    {
        $entityManager = $this->managerRegistry->getManager();

        $ingridient = $this->managerRegistry->getRepository(Ingridient::class)
            ->find($ingridientId);

        if (!$ingridient) {
            throw new \Exception('wrong ingridient');
        }

        $pizza->setIngridient($ingridient);

        $entityManager->persist($pizza);
        $entityManager->flush();
    }
}