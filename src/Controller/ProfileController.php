<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Mime\Address;

use App\Repository\UserRepository;
use App\Repository\BookingRepository;
use App\Form\UserUpdateType;
use App\Form\UpdatePasswordType;
use App\Entity\User;
use App\Entity\Booking;
use Doctrine\ORM\EntityManagerInterface;

class ProfileController extends AbstractController
{
    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/profil', name: 'app_profile', methods:['GET', 'POST'])]
    public function profileResume(Security $security): Response
    {
        $user = $security->getUser();
        $isValid = $user->isVerified();

        if(isset($_POST['verification'])&&!$isValid){
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address('contact.webdealmarket@gmail.com', 'Web Deal Market Vérification'))
                ->to($user->getEmail())
                ->subject('Confirmation de votre adresse-mail')
                ->htmlTemplate('emails/confirmation_email.html.twig')
            );
            $this->addFlash('notice', 'Veuillez confirmer votre compte en vérifiant votre adresse mail');
            return $this->redirectToRoute('app_base');

        }

        return $this->render('profile/resume.html.twig', ['user'=>$user]);
    }

    #[Route('/profil/update-password', name: 'app_profile_update_password', methods: ['POST', 'GET'])]
    public function profilePasswordUpdate(UserPasswordHasherInterface $userPasswordHasher, Security $security, Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $user = $security->getUser();
        $userRepo = $entityManagerInterface->getRepository(User::class);
        $form = $this->createForm(UpdatePasswordType::class, $user);
        $form->handleRequest($request);

        $email = $user->getEmail();
        $dateCreation = $user->getDateCreationCompte();
        $nom = $user->getNom();
        $prenom = $user->getPrenom();

        if($form->isSubmitted()&&$form->isValid()){
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setEmail($email);
            $user->setNom($nom);
            $user->setPrenom($prenom);
            $user->setDateCreationCompte($dateCreation);

            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();
            
            $dateModif = new \DateTime();
            /*
            $email = (new TemplatedEmail())
            ->from(new Adress('contact.webdealmarket@gmail.com', 'Web Deal Market'))
            ->to($user->getEmail())
            ->subject('Information de modification de mot de passe')
            ->htmlTemplate('emails/update_password.html.twig')
            ->context([
                'email'=> $user->getEmail(),
                'dateModif'=>$dateModif,
            ]);
        
            $mailer->send($email);
            */

            $this->addFlash('success', 'Votre mot de passe a été modifié !');
            return $this->redirectToRoute('app_profile');
        }
        return $this->render('profile/updatePassword.html.twig',['form'=>$form->createView()]);
    }

    #[Route('/profil/update-infos', name: 'app_profile-update', methods: ['GET', 'POST'])]
    public function profileUpdate(Security $security, Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $user = $security->getUser();
        $userRepository = $entityManagerInterface->getRepository(User::class);

        $form = $this->createForm(UserUpdateType::class, $user);
        $form->handleRequest($request);
        
        $email = $user->getEmail();
        $password = $user->getPassword();
        $dateCreation = $user->getDateCreationCompte();

            if($form->isSubmitted()&&$form->isValid()){
                $user->setEmail($email);
                $user->setPassword($password);
                $user->setDateCreationCompte($dateCreation);
        
                $entityManagerInterface->persist($user);
                $entityManagerInterface->flush();
                
                $this->addFlash('success', 'Vos informations personnelles ont bien été modifiées !');
                return $this->redirectToRoute('app_profile');
            }
        return $this->render('profile/update.html.twig', ['user'=>$user, 'form'=>$form->createView()]);
    }

    #[Route('/admin/users-view', name: 'app_liste_user', methods:['GET'])]
    public function listeUser(EntityManagerInterface $entityManagerInterface, Request $request): Response
    {
        $repoUser = $entityManagerInterface->getRepository(User::class);
        if($request->get('id') != null){
            $lUtilisateurASupprimer = $entityManagerInterface->getRepository(User::class)->find($request->get('id'));
            $sesRendezVous = $entityManagerInterface->getRepository(Booking::class)->find($request->get('id'));

            $entityManagerInterface->remove($lUtilisateurASupprimer);
            $entityManagerInterface->flush();
            /*
            $email = (new TemplatedEmail())
            ->from(new Adress('contact.webdealmarket@gmail.com', 'Web Deal Market'))
            ->to($lUtilisateurASupprimer->getEmail())
            ->subject('Information de suppression de votre compte client')
            ->htmlTemplate('emails/suppression_compte.html.twig')
            ->context([
                'nom'=> $lUtilisateurASupprimer->getNom(),
                'prenom'=> $lUtilisateurASupprimer->getPrenom(),
                'email'=> $lUtilisateurASupprimer->getEmail(),
                'dateCreationCompte'=> $lUtilisateurASupprimer->getDateCreationCompte(),
            ]);
          
            $mailer->send($email);
            */
            $this->addFlash('danger','Utilisateur supprimé de la liste !');
            return $this->redirectToRoute('app_liste_user');
        }
        $users = $repoUser->findAll();

        return $this->render('profile/liste-user.html.twig', ['users'=>$users]);
    }
}
