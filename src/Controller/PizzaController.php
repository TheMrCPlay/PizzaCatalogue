<?php

namespace App\Controller;

use App\Service\PizzaCatalogue;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Pizza;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Ingridient;


class PizzaController extends AbstractController
{
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PizzaController.php',
        ]);
    }

    public function add(Request $request, ManagerRegistry $managerRegistry): JsonResponse
    {
        $entityManager = $managerRegistry->getManager();

        $pizzaName = $request->get('name');

        $pizza = new Pizza();
        $pizza->setName($pizzaName);

        $entityManager->persist($pizza);
        $entityManager->flush();

        return $this->json([
            'id' => $pizza->getId()
        ]);
    }


    /**
     * @param ManagerRegistry $managerRegistry
     * @param Request $request
     * @param Pizza $pizza
     * @return JsonResponse
     */
    public function addIngridient(ManagerRegistry $managerRegistry, Request $request, Pizza $pizza): JsonResponse
    {
        $entityManager = $managerRegistry->getManager();

        $ingridientName = $request->get('ingridient_name');
        $ingridientPrice = $request->get('ingridient_price');

        $ingridient = new Ingridient();
        $ingridient->setName($ingridientName);
        $ingridient->setPrice($ingridientPrice);

        $pizza->setIngridient($ingridient);

        $entityManager->persist($ingridient);
        $entityManager->persist($pizza);
        $entityManager->flush();

        return $this->json([
            'message' => 'New pizza ingridient added',
            'ingridient_id' => $ingridient->getId()
        ]);
    }

    public function apendIngridient(PizzaCatalogue $pizzaCatalogue, Request $request, Pizza $pizza): JsonResponse
    {
        $ingridientId = $request->get('ingridient_id');

        try {
            $pizzaCatalogue->appendIngridientById($pizza, $ingridientId);
        }
        catch (\Exception $ex) {
            return $this->json([
                'message' => $ex->getMessage()
            ], 400);
        }

        return $this->json([
            'message' => 'ingridient appended'
        ]);
    }

    public function info(PizzaCatalogue $pizzaCatalogue, Request $request, Pizza $pizza): JsonResponse
    {
        //return $this->json($pizzaCatalogue->getPizzaFullInfo($pizza));
        return $this->json($pizza);
    }
}