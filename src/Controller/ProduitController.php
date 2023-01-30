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
use App\Repository\MarqueProduitRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

use App\Entity\Produit;
use App\Entity\MarqueProduit;
use App\Entity\Fichier;
use App\Form\ProduitType;
use App\Form\UpdateProduitType;

class ProduitController extends AbstractController
{
    /*DEBUT DES FONCTIONS ADMIN*/
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
                $search = array('À','Á','Â','Ã','Ä','Å','Ç','È','É','Ê','Ë','Ì',
                'Í','Î','Ï','Ò','Ó','Ô','Õ','Ö','Ù','Ú','Û','Ü','Ý','à',
                'á','â','ã','ä','å','ç','è','é','ê','ë','ì','í','î','ï','ð',
                'ò','ó','ô','õ','ö','ù','ú','û','ü','ý','ÿ', ' ');
                $remplace = array('A','A','A','A','A','A','C','E','E','E','E','I',
                'I','I','I','O','O','O','O','O','U','U','U','U','Y','a','a','a',
                'a','a','a','c','e','e','e','e','i','i','i','i','o','o',
                'o','o','o','o','u','u','u','u','y','y','-');
                $subject = $slug;
                $slugFinal = str_replace($search, $remplace, $subject);
                $produit->setSlug($slugFinal);
                $produit->setIsFavourite(false);
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
    public function listeProduitAdmin(Filesystem $filesystem, EntityManagerInterface $entityManagerInterface, Request $request): Response
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

    #[Route('/private/product/{slug}', name: 'app_produit_show', methods: ['GET'])]
    public function voirProduitAdmin(string $slug, Request $request, Produit $produit): Response
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

    /*FIN DES FONCTIONS ADMIN*/

    /*DÉBUT DES FONCTIONS UTILISATEURS*/

    #[Route('/product/{slug}', name: 'app_product_public', methods: ['GET'])]
    public function voirProduit(Request $request, Produit $produit): Response
    {
        return $this->render('produit/produitById.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/nos-produits', name: 'app_product_list')]
    public function listeProduit(EntityManagerInterface $entityManagerInterface): Response
    {
        $repoProduit = $entityManagerInterface->getRepository(Produit::class);
        $produits = $repoProduit->findAll();
        return $this->render('produit/produitList.html.twig', ['produits'=>$produits]);
    }

    #[Route('/nos-produits/{libelle}', name: 'app_product_marque')]
    public function voirByMarque(MarqueProduit $marque, Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        
        return $this->render('produit/produitList.html.twig', ['produits'=>$marque->getProduits()]);
    }

    #[Route('/marques', name: 'app_marques')]
    public function listeMarque(EntityManagerInterface $entityManagerInterface): Response
    {
        $repoMarques = $entityManagerInterface->getRepository(MarqueProduit::class);
        $marques = $repoMarques->findAll();
        return $this->render('produit/marqueList.html.twig', ['marques'=>$marques]);
    }



    /*FIN DES FONCTIONS UTILISATEURS*/

}
