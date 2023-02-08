<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Carrier;
use App\Repository\CarrierRepository;
use App\Form\CarrierType;

class CarrierController extends AbstractController
{
    #[Route('/private-carrier', name: 'app_carrier')]
    public function management(EntityManagerInterface $entityManagerInterface, Request $request): Response
    {
        $carrier = new Carrier();
        $form = $this->createForm(CarrierType::class, $carrier);
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isSubmitted()&&$form->isValid()){
                $entityManagerInterface->persist($carrier);
                $entityManagerInterface->flush();
                $this->addFlash('success', 'Transporteur ajouté !');
                return $this->redirectToRoute('app_carrier');
            }
        }
        $repoCarrier = $entityManagerInterface->getRepository(Carrier::class);
        if($request->get('id') != null){
            $leTransporteur = $entityManagerInterface->getRepository(Carrier::class)->find($request->get('id'));
            $entityManagerInterface->remove($leTransporteur);
            $entityManagerInterface->flush();
            $this->addFlash('danger','Transporteur supprimé !');
            return $this->redirectToRoute('app_carrier');
        }
        $lesTransporteurs = $repoCarrier->findAll();
        return $this->render('carrier/index.html.twig', ['lesTransporteurs'=>$lesTransporteurs, 'form'=>$form->createView()]);
    }
}
