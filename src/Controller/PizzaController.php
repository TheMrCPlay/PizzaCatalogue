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
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;
use App\Form\Type\PizzaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\RequestStack;

class PizzaController extends AbstractController
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function index(ManagerRegistry $managerRegistry, PizzaCatalogue $pizzaCatalogue): Response
    {
        $pizzaList = new ArrayCollection($managerRegistry->getRepository(Pizza::class)->findAll());

        $marginPercent = $this->getParameter('app.margin_percent') ?? null;

        $pizzaList = $pizzaList->map(function (Pizza $pizza) use ($pizzaCatalogue, $marginPercent) {
            return $pizzaCatalogue->getPizzaFullInfo($pizza, $marginPercent);
        });

        return $this->render('catalogue/pizza/list.html.twig', [
            'page_title' => 'Pizza Catalogue',
            'pizza_list' => $pizzaList
        ]);
    }

    public function pizzaDetails(PizzaCatalogue $pizzaCatalogue, Pizza $pizza): Response
    {
        $session = $this->requestStack->getSession();
        $session->set('pizza_id_details', $pizza->getId());

        $ingridients = $pizzaCatalogue->getPizzaIngridients($pizza);

        $marginPercent = $this->getParameter('app.margin_percent') ?? null;
        $totalPrice = $pizzaCatalogue->getPizzaFullInfo($pizza, $marginPercent)['price'];

        return $this->render('catalogue/pizza/details.html.twig', [
            'page_title' => sprintf('"%s" details', $pizza->getName()),
            'ingridient_list' => $ingridients,
            'pizza_id' => $pizza->getId(),
            'total_price' => $totalPrice,
        ]);
    }

    public function new (Request $request, PizzaCatalogue $pizzaCatalogue): Response
    {
        $pizza = new Pizza();

        $form = $this->createForm(PizzaType::class , $pizza);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $pizza = $form->getData();

            try {
                $pizzaCatalogue->updatePizza($pizza);
            }
            catch (\Exception $ex) {
                $this->addFlash('warning', $ex->getMessage());
            }

            return $this->redirectToRoute('index');
        }

        return $this->renderForm('catalogue/pizza/new.html.twig', [
            'page_title' => 'Add new pizza',
            'form' => $form,
        ]);
    }

    public function update(ManagerRegistry $managerRegistry, PizzaCatalogue $pizzaCatalogue, Request $request, Pizza $pizza): Response
    {
        $form = $this->createForm(PizzaType::class , $pizza);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $pizza = $form->getData();

            try {
                $pizza = $pizzaCatalogue->updatePizza($pizza);
            }
            catch (\Exception $ex) {
                $this->addFlash('warning', $ex->getMessage());
            }

            return $this->redirectToRoute('index');
        }

        $this->addFlash('notice', sprintf('Pizza "%s" updated', $form->getData()->getName()));

        return $this->renderForm('catalogue/pizza/new.html.twig', [
            'page_title' => 'Update ingridient',
            'form' => $form,
        ]);
    }

    public function pizzaAppendIngridientList(Request $request, ManagerRegistry $managerRegistry, Pizza $pizza): Response
    {
        $session = $this->requestStack->getSession();
        $session->set('pizza_id_append_list', $pizza->getId());

        $ingridientList = $managerRegistry->getRepository(Ingridient::class)->findAll();

        return $this->renderForm('catalogue/pizza/ingridient_append_list.html.twig', [
            'page_title' => 'Append ingridient',
            'ingridient_list' => $ingridientList,
            'pizza_id' => $pizza->getId(),
        ]);
    }

    /**
     * @ParamConverter("pizza", options={"id" = "pizza_id"})
     * @ParamConverter("ingridient", options={"id" = "ingridient_id"})
     */
    public function apendIngridient(PizzaCatalogue $pizzaCatalogue, Pizza $pizza, Ingridient $ingridient): Response
    {
        try {
            $pizzaCatalogue->appendIngridientToPizza($pizza, $ingridient);
        }
        catch (\Exception $ex) {
            $this->addFlash('warning', $ex->getMessage());
        }

        return $this->redirectToRoute('details', [
            'id' => $pizza->getId(),
        ]);
    }

    /**
     * @ParamConverter("pizza", options={"id" = "pizza_id"})
     * @ParamConverter("ingridient", options={"id" = "ingridient_id"})
     */
    public function removeIngridient(PizzaCatalogue $pizzaCatalogue, Pizza $pizza, Ingridient $ingridient): Response
    {
        $pizzaCatalogue->removeIngridientFromPizza($pizza, $ingridient);

        $this->addFlash('notice', sprintf('"%s" removed', $ingridient->getName()));

        return $this->redirectToRoute('details', [
            'id' => $pizza->getId(),
        ]);
    }

    public function remove(Request $request, PizzaCatalogue $pizzaCatalogue, Pizza $pizza): Response
    {

        $pizzaCatalogue->removePizza($pizza);
        $this->addFlash('notice', sprintf('"%s" removed', $pizza->getName()));

        return $this->redirectToRoute('index');
    }
}