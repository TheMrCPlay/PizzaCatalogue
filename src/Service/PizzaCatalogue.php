<?php

namespace App\Service;

use App\Entity\Pizza;
use App\Entity\Ingridient;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\Migrations\Tools\Console\Exception\VersionDoesNotExist;
use Doctrine\Persistence\ManagerRegistry;
use App\Dto\PizzaFullInfo;
use phpDocumentor\Reflection\Types\Void_;
use Symfony\Component\DependencyInjection\Compiler\CheckExceptionOnInvalidReferenceBehaviorPass;

class PizzaCatalogue
{
    private $serializer;
    private $managerRegistry;

    public function __construct(SerializerInterface $serializer, ManagerRegistry $managerRegistry)
    {
        $this->serializer = $serializer;
        $this->managerRegistry = $managerRegistry;
    }

    public function appendIngridientToPizza(Pizza $pizza, Ingridient $ingridient): void
    {
        $entityManager = $this->managerRegistry->getManager();

        $exists = $pizza->getIngridients()->exists(function ($key, $element) use ($ingridient) {
            return $element->getId() === $ingridient->getId();
        });

        if ($exists) {
            throw new \Exception('Ingridient already appended');
        }

        $pizza->setIngridient($ingridient);
        $entityManager->persist($pizza);
        $entityManager->flush();
    }

    public function removeIngridientFromPizza(Pizza $pizza, Ingridient $ingridient): void
    {
        $entityManager = $this->managerRegistry->getManager();

        $pizza->unsetIngridient($ingridient);
        $entityManager->persist($pizza);
        $entityManager->flush();
    }

    public function updatePizza(Pizza $pizza): void
    {
        $existingPizza = $this->managerRegistry->getRepository(Pizza::class)
            ->findBy([
            'Name' => $pizza->getName()
        ]);

        if ($existingPizza && (!$existingPizza[0]->getId() || $pizza->getId() !== $existingPizza[0]->getId())) {
            throw new \Exception('Pizza with that name already exists');
        }

        $entityManager = $this->managerRegistry->getManager();

        $entityManager->persist($pizza);
        $entityManager->flush();
    }

    public function updateIngridient(Ingridient $ingridient): void
    {
        $existingIngridient = $this->managerRegistry->getRepository(Ingridient::class)
            ->findBy([
            'Name' => $ingridient->getName()
        ]);

        if ($existingIngridient && (!$existingIngridient[0]->getId() || $ingridient->getId() !== $existingIngridient[0]->getId())) {
            throw new \Exception('Ingridient with that name already exists');
        }

        $entityManager = $this->managerRegistry->getManager();

        $entityManager->persist($ingridient);
        $entityManager->flush();
    }

    public function removeIngridient(Ingridient $ingridient): void
    {
        $entityManager = $this->managerRegistry->getManager();
        $entityManager->remove($ingridient);
        $entityManager->flush();
    }

    public function removePizza(Pizza $pizza): void
    {
        $entityManager = $this->managerRegistry->getManager();
        $entityManager->remove($pizza);
        $entityManager->flush();
    }

    public function getPizzaFullInfo(Pizza $pizza, float $marginPercent = 50): array
    {
        $ingridientPriceCost = 0;

        $ingridients = $this->getPizzaIngridients($pizza);

        foreach ($ingridients as $ingridient) {
            $ingridientPriceCost += $ingridient->getPrice();
        }

        $formulaResult = $ingridientPriceCost + ($ingridientPriceCost * ($marginPercent / 100));

        return [
            'id' => $pizza->getId(),
            'name' => $pizza->getName(),
            'price' => number_format($formulaResult, 2, '.', ''),
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