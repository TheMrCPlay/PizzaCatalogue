<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Ingridient;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Type\IngridientType;
use Symfony\Component\HttpFoundation\Response;
use App\Service\PizzaCatalogue;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\RequestStack;

class IngridientController extends AbstractController
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function index(ManagerRegistry $managerRegistry): Response
    {
        $this->requestStack->getSession()->remove('pizza_id_details');
        $this->requestStack->getSession()->remove('pizza_id_append_list');

        $ingridientList = $managerRegistry->getRepository(Ingridient::class)->findAll();

        return $this->render('catalogue/ingridient/list.html.twig', [
            'page_title' => 'List of all ingridients',
            'ingridient_list' => $ingridientList
        ]);
    }

    public function new (Request $request, PizzaCatalogue $pizzaCatalogue): Response
    {
        $ingridient = new Ingridient();

        $form = $this->createForm(IngridientType::class , $ingridient);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $ingridient = $form->getData();

            try {
                $pizzaCatalogue->updateIngridient($ingridient);
            }
            catch (\Exception $ex) {
                $this->addFlash('notice', $ex->getMessage());
            }

            $session = $this->requestStack->getSession();

            if ($session->has('pizza_id_append_list')) {

                return $this->redirectToRoute('ingridient_pizza_append_ingridient_list', [
                    'id' => $session->get('pizza_id_append_list'),
                ]);
            }


            return $this->redirectToRoute('ingridient_list');
        }

        return $this->renderForm('catalogue/ingridient/new.html.twig', [
            'page_title' => 'Add new ingridient',
            'form' => $form,
        ]);
    }

    public function update(ManagerRegistry $managerRegistry, PizzaCatalogue $pizzaCatalogue, Request $request, Ingridient $ingridient): Response
    {
        $form = $this->createForm(IngridientType::class , $ingridient);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $ingridient = $form->getData();

            try {
                $pizzaCatalogue->updateIngridient($ingridient);
            }
            catch (\Exception $ex) {
                $this->addFlash('notice', $ex->getMessage());
            }

            $session = $this->requestStack->getSession();

            if ($session->has('pizza_id_details')) {

                return $this->redirectToRoute('details', [
                    'id' => $session->get('pizza_id_details'),
                ]);
            }

            return $this->redirectToRoute('ingridient_list');
        }

        $this->addFlash('notice', sprintf('Ingridient "%s" saved', $ingridient->getName()));

        return $this->renderForm('catalogue/ingridient/new.html.twig', [
            'page_title' => 'Update ingridient',
            'form' => $form,
        ]);
    }

    public function remove(Request $request, PizzaCatalogue $pizzaCatalogue, Ingridient $ingridient): Response
    {
        $pizzaCatalogue->removeIngridient($ingridient);
        $this->addFlash('notice', sprintf('"%s" removed', $ingridient->getName()));

        $session = $this->requestStack->getSession();
        if ($session->has('pizza_id_details')) {

            return $this->redirectToRoute('details', [
                'id' => $session->get('pizza_id_details'),
            ]);
        }

        return $this->redirectToRoute('ingridient_list');
    }
}
