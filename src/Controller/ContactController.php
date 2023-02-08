<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Address;

use App\Form\ContactType;
use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
   
use Symfony\Component\Validator\Validator\ValidatorInterface;


class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function prendreContact(Request $request, EntityManagerInterface $entityManagerInterface, ValidatorInterface $validator): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isSubmitted()&&$form->isValid()){
                    $contact->setDateContact(new \Datetime());
                    $entityManagerInterface->persist($contact);
                    $entityManagerInterface->flush();
                    /*
                    $email = (new TemplatedEmail())
                    ->from(new Adress('contact.webdealmarket@gmail.com', 'Web Deal Market'))
                    ->to($contact->getEmail())
                    ->subject('Votre demande de contact')
                    ->htmlTemplate('emails/contact.html.twig')
                    ->context([
                        'email'=> $contact->getEmail(),
                        'dateContact'=> $contact->getDateContact(),
                        'texte'=> $contact->getTexte(),
                    ]);
                
                    $mailer->send($email);
                    */
                    $this->addFlash('notice', 'Message envoyé !');
                    return $this->redirectToRoute('app_contact');
                }else{
                    $this->addFlash('danger', 'Formulaire de contact invalide, échec de l\'envoi !');
                }
            }
        return $this->render('contact/prendrecontact.html.twig', [ 'form' => $form->createView()]);
    }
 
    #[Route('/private-liste-contact', name: 'app_liste_contact')]
    public function listeContact(EntityManagerInterface $entityManagerInterface, Request $request): Response{
        $repoContact = $entityManagerInterface->getRepository(Contact::class);
        if($request->get('id') != null){
            $leContactASupprimer = $entityManagerInterface->getRepository(Contact::class)->find($request->get('id'));
            $entityManagerInterface->remove($leContactASupprimer);
            $entityManagerInterface->flush();
            $this->addFlash('danger','Contact supprimé de la liste !');
            return $this->redirectToRoute('app_liste_contact');
        }
        $contacts = $repoContact->findAll();
        return $this->render('contact/liste-contact.html.twig', ['contacts'=>$contacts]);
    }
}
