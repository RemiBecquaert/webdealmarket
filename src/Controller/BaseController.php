<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

use App\Repository\ProduitRepository;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;

class BaseController extends AbstractController
{
    #[Route('/', name: 'app_base')]
    public function index(EntityManagerInterface $entityManagerInterface, Request $request): Response
    {
        $repoProduit = $entityManagerInterface->getRepository(Produit::class);
        $favouriteProduct = $repoProduit->findBy(['isFavourite' => true]);
        
        return $this->render('base/index.html.twig', ['favouriteProduct'=>$favouriteProduct]);
    }

    #[Route('/reparer', name: 'app_reparer')]
    public function reparer(): Response
    {
        return $this->render('base/reparer.html.twig', []);
    }

    #[Route('/mentions', name: 'app_mentions')]
    public function mentions(): Response
    {
        return $this->render('base/mentions.html.twig', []);
    }

    #[Route('/cgv', name: 'app_cgv')]
    public function cgv(): Response
    {
        return $this->render('base/cgv.html.twig', []);
    }

    #[Route('/politique-de-confidentialite', name: 'app_politique_confidentialite')]
    public function politique(): Response
    {
        return $this->render('base/politique.html.twig', []);
    }

    #[Route('/error-404', name: 'app_error_404')]
    public function error404(): Response
    {
        return $this->render('base/error.html.twig', []);
    }

    #[Route('/admin/control-panel', name: 'app_control_panel')]
    public function controlPanel(): Response
    {
        return $this->render('base/control-panel.html.twig', []);
    }
}
