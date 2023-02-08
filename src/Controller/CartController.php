<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;


class CartController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }


    #[Route('/profile-mon-panier', name: 'app_cart')]
    public function index(Cart $cart): Response
    {
        return $this->render('cart/index.html.twig', ['cart'=>$cart->getFull()]);
    }

    #[Route('/profile-cart-add-{id}', name: 'app_add_to_cart')]
    public function cartAdd(Cart $cart, $id): Response
    {
        $cart->add($id);
        $this->addFlash('success', 'Produit ajouté au panier !');

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/profile-cart-remove', name: 'app_remove_cart')]
    public function cartRemove(Cart $cart): Response
    {
        $cart->remove();
        $this->addFlash('success', 'Panier supprimé !');
        return $this->redirectToRoute('app_base');
    }

    #[Route('/profile-cart-decrease-{id}', name: 'app_decrease_cart')]
    public function cartDecrease(Cart $cart, $id): Response
    {
        $cart->decrease($id);
        $this->addFlash('danger', 'Produit supprimé !');
        return $this->redirectToRoute('app_cart');
    }
}
