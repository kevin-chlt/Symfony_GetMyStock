<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SubscribeType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


class UserController extends AbstractController
{
    #Display the form in GET method, check and persist the new User in POST method
    #[Route('/subscribe', name: 'subscribe-page', methods: ['GET', 'POST'])]
    public function getFormOrSubscription (Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $encoder) : Response
    {
        if ($this->isGranted('ROLE_MEMBER')) {
            return $this->redirectToRoute('products_index');
        }

        $user = new User();
        $form = $this->createForm(SubscribeType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            #VERIFICATION SI USERNAME OU EMAIL DEJA UTILISE
            $userRepository = $entityManager->getRepository(User::class);
           if($userRepository->checkMailAndUsernameUniq($user->getMail(), $user->getUserIdentifier())){
               $this->addFlash('error', 'Email ou nom d\'utilisateur déjà utilisée, veuillez en saisir une nouvelle ou vous connectez avec celle-ci');
           } else {
               $user->setPassword($encoder->hashPassword($user, $user->getPassword()))
                   ->setAccountKey(uniqid());

               $entityManager->persist($user);
               $entityManager->flush();
               return $this->RedirectToRoute('mail-sender', ['id' => $user->getId()]);
           }
        }

        return $this->render('user_subscription/subscribe.html.twig',[
            'form' => $form->createView()
            ]);
    }

    #Send a mail to the new User with activation link.
    #[Route('/send-mail/{id}', name: 'mail-sender')]
    public function sendMail (MailerInterface $mailer, string $id, UserRepository $repository) : Response
    {
        $user = $repository->find($id);

        $email = new TemplatedEmail();
        $email->from('kevin.challit@gmail.com')
            ->to(new Address($user->getMail(), $user->getUserIdentifier()))
            ->subject('Validation de compte GetMyStock')
            ->htmlTemplate('mail/subscribe-confirm-mail.html.twig')
            ->context([
                'expiration_link' => new \DateTime('+7 days'),
                'user' => $user
            ])
        ;
        $mailer->send($email);
        $this->addFlash('success', 'Votre inscription est enregistré, veuillez consulter votre boite mail et cliqué sur le lien de confirmation');

        return $this->redirectToRoute('app_login');
    }

    #Check the link key with account key user
    #[Route('confirmation/{id}/{key}', name: 'confirm-page')]
    public function setAccountConfirmed (string $id, string $key, UserRepository $repository, EntityManagerInterface $entityManager) : Response
    {
        $user = $repository->find($id);

        if ($key === $user->getAccountKey()) {
           $user->setRoles(['ROLE_MEMBER']);

            $entityManager->flush();
            $this->addFlash('success', 'Votre adresse à bien été confirmé, vous pouvez désormais accéder au site');
            return $this->redirectToRoute('app_login');
        }
        $this->addFlash('error', 'La clé ne fonctionne pas, merci de nous contactez afin de pouvoir vous redonner une clé fonctionnelle');
        return $this->redirectToRoute('app_login');
    }
}