<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile-resume', name: 'app_profile')]
    public function profileResume(Security $security): Response
    {
        $user = $security->getUser();

        return $this->render('profile/resume.html.twig', ['user'=>$user]);
    }
}
