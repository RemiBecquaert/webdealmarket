<?php

namespace App\Classe;

use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Cart{

    private $requestStack;
    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack){
        $this->requestStack = $requestStack;    
        $this->entityManager = $entityManager;
    }

    public function add($id){
        $session = $this->requestStack->getSession();

        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $session->set('cart', $cart);
    }

    public function decrease($id){
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);
        
        if($cart[$id] > 1){
            $cart[$id]--;
        } else{
            unset($cart[$id]);
        }
        return $session->set('cart', $cart);
    }

    public function get()
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart');

        return($cart);
    }

    public function remove()
    {
        $session = $this->requestStack->getSession();
        $cart = $session->remove('cart');

        return $cart;
    }

    public function getById($id){
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        return $cart[$id];
    }

    /*cette fonction nous permet d'afficher tous les produits contenus dans le panier
    on crée un tableau vide cartComplete, si on récupère le tableau associatif cart contenu dans la session de l'utilisateur,
    on boucle pour chaque produit dans le tableau associatif cart
    si le produit n'existe pas, donc est vide, on supprime le panier, sinon on ajoute les valeurs dans le tableau cartComplete
    enfin, on retourne le tableau cartComplete
    */
    public function getFull()
    {
        $cartComplete = [];

        if ($this->get()) {
            foreach ($this->get() as $id => $quantity) {
                $product_object = $this->entityManager->getRepository(Produit::class)->findOneById($id);

                if (!$product_object) {
                    $this->delete($id);
                    continue;
                }

                $cartComplete[] = [
                    'produit' => $product_object,
                    'quantity' => $quantity
                ];
            }
        }

        return $cartComplete;
    }
}