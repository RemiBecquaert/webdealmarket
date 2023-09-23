<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Dompdf\Dompdf;
use Dompdf\Options;

use App\Form\OrderType;

use App\Classe\Cart;

use App\Entity\Produit;
use App\Entity\Carrier;
use App\Entity\Address;
use App\Entity\Order;
use App\Entity\OrderDetails;

use App\Repository\OrderRepository;

class OrderController extends AbstractController
{

    private $entityManagerInterface;

    public function __construct(EntityManagerInterface $entityManagerInterface){
        $this->entityManagerInterface = $entityManagerInterface;
    }

    #[Route('/commande-{id}', name: 'app_order_by_id_user')]
    public function orderByIdUser(Security $security, Order $order, Request $request, $id): Response
    {
        $user = $security->getUser();

        $order = $this->entityManagerInterface->getRepository(Order::class)->find($id);
        $lesDetails = $this->entityManagerInterface->getRepository(OrderDetails::class)->findAll();

        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_order_management_user');
        }  
        return $this->render('order/voirCommandeUser.html.twig', ['order'=>$order, 'orderDetails'=>$lesDetails]);
    }

    #[Route('/admin/commande-{id}', name: 'app_order_management_admin')]
    public function orderByIdAdmin(Order $order): Response
    {
        return $this->render('order/order-by-id.html.twig', ['order'=>$order, 'orderDetails'=>$order->getOrderDetails()]);
    }

    #[Route('/commande/view', name: 'app_order_management_user')]
    public function ordersManage(Security $security, Request $request): Response
    {
        $user = $security->getUser();

        $repoOrder = $this->entityManagerInterface->getRepository(Order::class);
        $repoDetails = $this->entityManagerInterface->getRepository(OrderDetails::class);
        if($request->get('id') != null){
            $orderASupp = $this->entityManagerInterface->getRepository(Order::class)->find($request->get('id'));
            $entityManagerInterface->remove($orderASupp);
            $entityManagerInterface->flush();
            $this->addFlash('danger','Commande supprimée !');
            return $this->redirectToRoute('app_order_management');
        }
        $lesCommandes = $repoOrder->findBy(['user' => $user]);
        $lesDetails = $repoDetails->findAll();

        return $this->render('order/orderManagement.html.twig', ['orders'=>$lesCommandes, 'orderDetails'=>$lesDetails]);
    }

    #[Route('/commande/summary', name: 'app_order_recap', methods: ['POST', 'GET'])]
    public function orderAdd(Cart $cart, Request $request): Response
    {
        
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);
        $changementPrixLivraison = 150;

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
                $order->setDelivery($delivery_content);
                $order->setIsPaid(0);    
                $totalCommande = 0;
                foreach ($cart->getFull() as $product){
                    $orderDetails = new OrderDetails();
                    $orderDetails->setMyOrder($order);
                    $orderDetails->setProduct($product['produit']->getLibelle());
                    $orderDetails->setQuantity($product['quantity']);
                    $orderDetails->setPrice($product['produit']->getPrix());
                    $orderDetails->setTotal($product['produit']->getPrix() * $product['quantity']);
                    $totalCommande += $orderDetails->getTotal();
                    $this->entityManagerInterface->persist($orderDetails);
                }  
                if($totalCommande > $changementPrixLivraison){
                    $order->setCarrierPrice($carriers->getPrixFactureSup());
                } else{
                    $order->setCarrierPrice($carriers->getPrix());
                }
                $this->entityManagerInterface->persist($order);
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

    #[Route('/admin/commandes-view', name: 'app_order_admin_view')]
    public function adminOrderView(OrderRepository $orders): Response
    {
        return $this->render('order/orders-view.html.twig', ['orders'=>$orders->findAll()]);
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
}
