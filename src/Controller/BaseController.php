<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;


class BaseController extends AbstractController
{
    #[Route('/', name: 'app_base')]
    public function index(): Response
    {
        return $this->render('base/index.html.twig', []);
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
}
