<?php

namespace App\Controller;

use App\Entity\Booking;
use Symfony\Component\Security\Core\Security;
use App\Form\BookingType;
use App\Form\BookingType2;
use App\Repository\BookingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;


class BookingController extends AbstractController
{
    public function __construct(EntityManagerInterface $entityManagerInterface){
        $this->entityManagerInterface = $entityManagerInterface;
    }

    #[Route('/admin/rdv-show-{id}', name: 'app_booking_show', methods: ['GET'])]
    public function show(Booking $booking): Response
    {
        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
        ]);
    }

    #[Route('/admin/rdv-edit-{id}', name: 'app_booking_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Booking $booking, EntityManagerInterface $entityManagerInterface): Response
    {
        $form = $this->createForm(BookingType2::class, $booking);
        $form->handleRequest($request);

        $description = $booking->getDescription();
        $beginAt = $booking->getBeginAt();
        $sujetId = $booking->getSujetId();
        $idClient = $booking->getIdClient();
        $dateCreation = $booking->getDateCreation();

        if ($form->isSubmitted() && $form->isValid()) {
            $booking->setDescription($description);
            $booking->setBeginAt($beginAt);
            $booking->setSujetId($sujetId);
            $booking->setIdClient($idClient);
            $booking->setDateCreation($dateCreation);

            $entityManagerInterface->persist($booking);
            $entityManagerInterface->flush();

            $this->addFlash('success', 'Le rendez-vous a bien été validé !');

            return $this->redirectToRoute('app_booking_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('booking/edit.html.twig', [
            'booking' => $booking,
            'form' => $form,
        ]);
    }

    #[Route('/admin/rdv-delete-{id}', name: 'app_booking_delete', methods: ['POST'])]
    public function delete(Request $request, Booking $booking, BookingRepository $bookingRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$booking->getId(), $request->request->get('_token'))) {
            $bookingRepository->remove($booking, true);
        }

        return $this->redirectToRoute('app_booking_index', [], Response::HTTP_SEE_OTHER);
    } 
    
    #[Route('/reparation', name: 'app_booking_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Security $security, EntityManagerInterface $entityManagerInterface): Response
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);

        $user = $security->getUser();

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                $booking->setIdClient($user);
                $booking->setIsConfirmed(false);
                $booking->setDateCreation(new \Datetime());
                $entityManagerInterface->persist($booking);
                $entityManagerInterface->flush();
                /*
                $email = (new TemplatedEmail())
                ->from($user->getEmail())
                ->to('contact.webdealmarket@gmail.com')
                ->subject('DEMANDE RÉPARATION')
                ->htmlTemplate('emails/new_booking.html.twig')
                ->context([
                    'email'=> $user->getEmail(),
                    'dateCreation'=> $booking->getDateCreation(),
                    'dateProposition'=> $booking->getBeginAt(),
                    'description'=> $booking->getDescription(),
                ]);
            
                $mailer->send($email);
                */
                $this->addFlash('notice', 'La prise de rendez-vous a bien été enregistrée, nous reviendrons rapidement vers vous pour la valider !');
    
                return $this->redirectToRoute('app_reparer', [], Response::HTTP_SEE_OTHER);
            }
        }
        return $this->renderForm('booking/new.html.twig', ['form' => $form, 'user'=>$user]);
    }

    #[Route('/admin/rdv-liste', name: 'app_booking_index', methods: ['GET'])]
    public function index(BookingRepository $bookingRepository): Response
    {
        return $this->render('booking/index.html.twig', [
            'bookings' => $bookingRepository->findAll(),
        ]);
    }

    #[Route('/admin/rdv-valids', name: 'app_booking_valids', methods: ['GET'])]
    public function getValidsBookings(EntityManagerInterface $entityManagerInterface, Request $request): Response{
        $repoBooking = $entityManagerInterface->getRepository(Booking::class);
        $validsBooking = $repoBooking->findBy(['isConfirmed' => true]);

        return $this->render('booking/valid-list.html.twig', ['validsBooking'=>$validsBooking]);
    }    

    #[Route('/profil/mes-rdv', name: 'app_user_bookings')]
    public function getUserBookings(Security $security, Request $request){
        $user = $security->getUser();
        $userBooking = $this->entityManagerInterface->getRepository(Booking::class)->findBy(['idClient' => $user]);  

        if($request->get('id') != null){
            $deleteBooking = $this->entityManagerInterface->getRepository(Booking::class)->find($request->get('id'));
            if (!$deleteBooking || $deleteBooking->getIdClient() != $this->getUser()) {
            $this->addFlash('danger','Erreur dans la reqûete !');
            return $this->redirectToRoute('app_profile');
            }
            $this->entityManagerInterface->remove($deleteBooking);
            $this->entityManagerInterface->flush();
            $this->addFlash('danger','Rendez-vous supprimé !');
            return $this->redirectToRoute('app_user_bookings');
        }
        return $this->render('booking/userBookings.html.twig', ['bookings'=>$userBooking]);
    }
}
