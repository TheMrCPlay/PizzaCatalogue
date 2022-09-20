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

        $ingridientName = $request->query->get('name');
        $ingridientPrise = $request->query->get('price');

        $ingridient = new Ingridient();
        $ingridient->setName($ingridientName);
        $ingridient->setPrice($ingridientPrise);

        $entityManager->persist($ingridient);
        $entityManager->flush();

        return $this->json([
            'id' => $ingridient->getId()
        ]);
    }

    public function remove(): JsonResponse
    {
        return $this->json([]);
    }
}
