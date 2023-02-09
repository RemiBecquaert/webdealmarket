<?php

namespace App\Controller;

use App\Form\OrderType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Produit;
use App\Classe\Cart;
use App\Entity\Carrier;
use App\Entity\Address;
use App\Entity\Order;
use App\Entity\OrderDetails;

class OrderController extends AbstractController
{

    private $entityManagerInterface;

    public function __construct(EntityManagerInterface $entityManagerInterface){
        $this->entityManagerInterface = $entityManagerInterface;
    }

    #[Route('/commande', name: 'app_order')]
    public function commande(Cart $cart): Response
    {
        if(!$this->getUser()->getAddresses()->getValues()){
            return $this->redirectToRoute('app_address_add');
        }
        if(!$cart->getFull()){
            return $this->redirectToRoute('app_cart');
        }

        /*pour chaque produit dans le panier
        je veux récupérer l'identifiant d'un produit dans le panier
        je veux récupérer la quantité du produit en question produit dans le panier
        je veux associer le produit de la db avec l'id du produit du panier
        je veux comparer la quantité du produit du panier avec la quantité du produit de la db*/

        foreach ($cart->getFull() as $product){
            $productId = $product['produit'];
            $productQuantity = $product['quantity'];
            $product = $this->entityManagerInterface->getRepository(Produit::class)->find($productId);

            if ($product->getQuantite() < $productQuantity) {
                $this->addFlash('notice', 'La quantité du produit n\'est pas valide !');
                return $this->redirectToRoute('app_cart');
            }
        }

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);
        
        return $this->render('order/index.html.twig', ['form' =>$form->createView(), 'cart'=>$cart->getFull()]);
    }

    #[Route('/commande/summary', name: 'app_order_recap', methods: ['POST', 'GET'])]
    public function add(Cart $cart, Request $request): Response
    {
        
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        try{
            $form->handleRequest($request);
            if($form->isSubmitted()&&$form->isValid()){ 
                $date = new \DateTime();
                $carriers = $form->get('carriers')->getData();
                $delivery = $form->get('addresses')->getData();
                $delivery_content = $delivery->getFirstName().' '.$delivery->getLastName().' '.$delivery->getPhone(); 
    
                if($delivery->getCompany()){
                    $delivery_content .= '<br/>'.$delivery->getCompany();
                }
    
                $delivery_content .= '<br/>'.$delivery->getAddress(); 
                $delivery_content .= '<br/>'.$delivery->getPostal().' '.$delivery->getCity().' '.$delivery->getCountry();
    
                $order = new Order();
                $order->setUser($this->getUser());
                $order->setCreatedAt($date);
                $order->setCarrierName($carriers->getNom());
                $order->setCarrierPrice($carriers->getPrix());
                $order->setDelivery($delivery_content);
                $order->setIsPaid(0);
    
                $this->entityManagerInterface->persist($order);
    
                foreach ($cart->getFull() as $product){
                    $orderDetails = new OrderDetails();
                    $orderDetails->setMyOrder($order);
                    $orderDetails->setProduct($product['produit']->getLibelle());
                    $orderDetails->setQuantity($product['quantity']);
                    $orderDetails->setPrice($product['produit']->getPrix());
                    $orderDetails->setTotal($product['produit']->getPrix() * $product['quantity']);
                    $this->entityManagerInterface->persist($orderDetails);
                
                }
                $this->entityManagerInterface->flush();
            }
        return $this->render('order/add.html.twig', ['carrier'=>$carriers, 'adresse'=>$delivery, 'cart'=>$cart->getFull()]);

        }
        catch(\Exception $e){
            error_log($e->getMessage());
        }
        $this->addFlash('notice', 'Une erreur est survenue');
        return $this->redirectToRoute('app_cart');
    }
}
