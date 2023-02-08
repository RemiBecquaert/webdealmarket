<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

use App\Entity\User;
use App\Classe\Cart;
use App\Entity\Address;
use App\Repository\AddressRepository;
use App\Form\AddressType;

class AddressController extends AbstractController

{
    #[Route('/profile-gestion-adresse', name: 'app_address_management')]
    public function management(EntityManagerInterface $entityManagerInterface, Security $security, Request $request): Response
    {

        $user = $security->getUser();
        $repoAdresse = $entityManagerInterface->getRepository(Address::class);
        if($request->get('id') != null){
            $adresseASupp = $entityManagerInterface->getRepository(Address::class)->find($request->get('id'));
            $entityManagerInterface->remove($adresseASupp);
            $entityManagerInterface->flush();
            $this->addFlash('danger','Adresse supprimée !');
            return $this->redirectToRoute('app_address_management');
        }
        $lesAdresses = $repoAdresse->findBy(['user' => $user]);
        return $this->render('address/gestionAdresses.html.twig', ['adresse'=>$lesAdresses]);
    }

    #[Route('/profile-ajouter-adresse', name: 'app_address_add')]
    public function add(Cart $cart, Security $security, EntityManagerInterface $entityManagerInterface, Request $request): Response
    {
        $user = $security->getUser();
        $adresse = new Address();
        $form = $this->createForm(AddressType::class, $adresse);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $adresse->setUser($user);
                $entityManagerInterface->persist($adresse);
                $entityManagerInterface->flush();

                if($cart->get()){
                    return $this->redirectToRoute('app_order');
                } else {
                    $this->addFlash('success', 'Adresse ajoutée !');
                    return $this->redirectToRoute('app_address_management');
                }
            }
        }
        return $this->render('address/ajouterAdresse.html.twig', ['form'=>$form->createView()]);
    }
}
