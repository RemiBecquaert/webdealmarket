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
}
