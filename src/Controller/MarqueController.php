<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\MarqueProduit;
use App\Entity\CategorieProduit;
use App\Form\MarqueType;
use App\Repository\MarqueProduitRepository;

use App\Service\MarqueService;

class MarqueController extends AbstractController
{
    #[Route('/admin/brands-view', name: 'app_marque')]
    public function index(Request $request, EntityManagerInterface $entityManagerInterface, MarqueService $marqueService): Response
    {
        $marque = new MarqueProduit();
        $form = $this->createForm(MarqueType::class, $marque);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isSubmitted()&&$form->isValid()){
                $entityManagerInterface->persist($marque);
                $entityManagerInterface->flush();
                $this->addFlash('notice', 'Marque ajoutée !');
                return $this->redirectToRoute('app_marque');

            }
        }

        $repoMarque = $entityManagerInterface->getRepository(MarqueProduit::class);
        if($request->get('id') != null){
            $laMarque = $entityManagerInterface->getRepository(MarqueProduit::class)->find($request->get('id'));
            $entityManagerInterface->remove($laMarque);
            $entityManagerInterface->flush();
            $this->addFlash('danger','Marque supprimée de la liste !');
            return $this->redirectToRoute('app_marque');
        }
        return $this->render('marque/index.html.twig', ['form'=>$form->createView(), 'lesMarques'=> $marqueService->getPaginatedBrands()] );
    }

    #[Route('/brands', name: 'app_marques')]
    public function listeMarque(MarqueService $marqueService): Response
    {
        return $this->render('produit/marqueList.html.twig', ['marques'=>$marqueService->getPaginatedBrands()]);
    }
}
