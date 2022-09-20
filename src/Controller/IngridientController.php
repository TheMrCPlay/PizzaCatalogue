<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Ingridient;
use Symfony\Component\HttpFoundation\Request;

class IngridientController extends AbstractController
{
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/IngridientController.php',
        ]);
    }

    public function add(ManagerRegistry $managerRegistry, Request $request): JsonResponse
    {
        $entityManager = $managerRegistry->getManager();

        $ingridientName = $request->get('name');
        $ingridientPrice = $request->get('price');

        $ingridient = new Ingridient();
        $ingridient->setName($ingridientName);
        $ingridient->setPrice($ingridientPrice);

        $entityManager->persist($ingridient);
        $entityManager->flush();

        return $this->json([
            'id' => $ingridient->getId()
        ]);
    }

    public function update(ManagerRegistry $managerRegistry, Request $request, Ingridient $ingridient): JsonResponse
    {
        $entityManager = $managerRegistry->getManager();

        $ingridientName = $request->get('name');
        $ingridientPrice = $request->get('price');

        if (!$ingridientName) {
            $ingridientName = $ingridient->getName();
        }

        if (!$ingridientPrice) {
            $ingridientPrice = $ingridient->getPrice();
        }

        $ingridient->setName($ingridientName);
        $ingridient->setPrice($ingridientPrice);

        $entityManager->persist($ingridient);
        $entityManager->flush();

        return $this->json([
            'message' => 'Ingridient info updated',
            'id' => $ingridient->getId()
        ]);
    }

    public function remove(ManagerRegistry $managerRegistry, Ingridient $ingridient): JsonResponse
    {
        $entityManager = $managerRegistry->getManager();

        $entityManager->remove($ingridient);
        $entityManager->flush();

        // todo implement
        return $this->json([
            'message' => 'Ingridient removed'
        ]);
    }
}
