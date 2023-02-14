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
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Repository\OrderRepository;


class InvoiceController extends AbstractController
{

    public function __construct(EntityManagerInterface $entityManagerInterface){
        $this->entityManagerInterface = $entityManagerInterface;
    }

    #[Route('/commande-invoice-{id}', name: 'app_invoice_download')]
    public function getInvoicePdf(Security $security, $id){
        $user = $security->getUser();
        $order = $this->entityManagerInterface->getRepository(Order::class)->find($id);
        $lesDetails = $this->entityManagerInterface->getRepository(OrderDetails::class)->findAll();
        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('app_order_management_user');
        }

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);
        $pdf = new Dompdf($pdfOptions);
        $context = stream_context_create([
            'ssl' =>[
                'verify_peer' => FALSE,
                'verify_peer_name' => FALSE,
                'allow_self_signed' => TRUE
            ]
            ]);
        $pdf->setHttpContext($context);    

        $html = $this->renderView('invoices/invoice.html.twig', ['order'=>$order, 'orderDetails'=>$lesDetails]);
        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();
        
        $nomFichier = 'invoice-user-' . $this->getUser()->getId() . '-order-' . $id . 'pdf';

        $pdf->stream($nomFichier, [
            'Attachment' => true
        ]);

        return new Response($html);
    }
}
