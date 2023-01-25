<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\Security;
use App\Repository\FichierRepository;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

use App\Entity\Produit;
use App\Entity\Fichier;
use App\Form\ProduitType;
use App\Form\UpdateProduitType;

class ProduitController extends AbstractController
{
    #[Route('/private-ajout-produit', name: 'app_ajout_produit')]
    public function creerProduit(Security $security, Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $produit = new Produit();
        $user = $security->getUser();
        $form = $this->createForm(ProduitType::class, $produit);
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isSubmitted()&&$form->isValid()){
                $illustrations = $form->get('illustration')->getData();
                $libelleATransformer = $form->get('libelle')->getData();

                $slug = strtolower($libelleATransformer);
                $slugFinal = str_replace(' ', '-', $slug);
                $produit->setSlug($slugFinal);
                foreach($illustrations as $illustration){
                    $nomIllus = md5(uniqid()).'.'.$illustration->guessExtension();
                    $illustration->move($this->getParameter('file_directory'), $nomIllus);
                    $fichier = new Fichier();
                    $fichier->setNomServeur($nomIllus);
                    $fichier->setDateEnvoi(new \DateTime());
                    $produit->addIllustration($fichier);
                }
                $entityManagerInterface->persist($produit);
                $entityManagerInterface->persist($fichier);
                $entityManagerInterface->flush();

                $this->addFlash('notice','Le produit a bien été créé !');
                return $this->redirectToRoute('app_ajout_produit');
            }
        }
        return $this->render('produit/index.html.twig', ['form'=>$form->createView()]);
    }

    #[Route('/private-liste-produits', name: 'app_liste_produits')]
    public function listeProduit(Filesystem $filesystem, EntityManagerInterface $entityManagerInterface, Request $request): Response
    {
        $repoProduit = $entityManagerInterface->getRepository(Produit::class);
        if($request->get('id') != null){
            $leProduitASupprimer = $entityManagerInterface->getRepository(Produit::class)->find($request->get('id'));
            $lImage = $entityManagerInterface->getRepository(Fichier::class)->find($request->get('id'));
            $filesystem->remove('uploads/fichiers/'.$lImage());
            $entityManagerInterface->remove($leProduitASupprimer);
            $entityManagerInterface->flush();
            $this->addFlash('danger','Produit supprimé de la liste !');
            return $this->redirectToRoute('app_liste_produits');
        }
        $produits = $repoProduit->findAll();
        return $this->render('produit/liste-produits.html.twig', ['produits'=>$produits]);
    }

    #[Route('/private-{slug}', name: 'app_produit_show', methods: ['GET'])]
    public function voirProduit(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/private-update-{id}', name: 'app_produit_update', methods: ['GET', 'POST'])]
    public function updateProduit(Request $request, Produit $produit, EntityManagerInterface $entityManagerInterface): Response
    {
        $form = $this->createForm(UpdateProduitType::class, $produit);
        $form->handleRequest($request);

        if($form->isSubmitted()&&$form->isValid()){
            $entityManagerInterface->persist($produit);
            $entityManagerInterface->flush();

            $this->addFlash('success', 'Le produit a bien été modifié !');
            return $this->redirectToRoute('app_liste_produits');
        }

        return $this->render('produit/update-produit.html.twig', ['form'=>$form->createView(),
            'produit' => $produit,
        ]);
    }

    /*public function sortByPrice(Request $request): JsonResponse
    {
        $sortOrder = $request->query->get('sortOrder');
        $products = $this->getDoctrine()->getRepository(Produit::class)->findBy(array(), array('price' => $sortOrder));

        return new JsonResponse($products);
    }*/
}
