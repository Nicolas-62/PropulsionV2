<?php

namespace App\Controller;

use App\Controller\Frontoffice\UserCrudController;
use App\Entity\User;
use App\Form\DefinePasswordType;
use App\Form\RegistrationFormType;
use App\Notification\BoNotification;
use App\Repository\UserRepository;
use App\Security\AppCustomAuthenticator;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class UserController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * RDD
     * fonction de génération de hash pour les utilisateurs
     *
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @return Response
     */
    #[Route(path: '/users/generateHash', name: 'user_generate_hash')]
    public function generateHash(EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        foreach($users as $user){
            $user->setHash(md5($user->getEmail()));
            $entityManager->persist($user);
        }
        $entityManager->flush();
        $this->addFlash('success', 'Les  utilisateurs ont bien un hash');

        return $this->redirectToRoute('bo_home');
    }

    
//    #[Route(path: '/user/login', name: 'user_login')]
//    public function login(AuthenticationUtils $authenticationUtils): Response
//    {
//        if ($this->getUser()) {
//            return $this->redirectToRoute('bo_home');
//        }
//
//        // get the login error if there is one
//        $error = $authenticationUtils->getLastAuthenticationError()?->getMessage();
//        // last username entered by the user
//        $lastUsername = $authenticationUtils->getLastUsername();
//
//        return $this->render('user/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
//    }


    #[Route(path: '/user/login', name: 'user_login')]

    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@EasyAdmin/page/login.html.twig', [
            // parameters usually defined in Symfony login forms
            'error' => $error,
            'last_username' => $lastUsername,

            // OPTIONAL parameters to customize the login form:

            // the translation_domain to use (define this option only if you are
            // rendering the login template in a regular Symfony controller; when
            // rendering it from an EasyAdmin Dashboard this is automatically set to
            // the same domain as the rest of the Dashboard)
//            'translation_domain' => null,

            // by default EasyAdmin displays a black square as its default favicon;
            // use this method to display a custom favicon: the given path is passed
            // "as is" to the Twig asset() function:
            // <link rel="shortcut icon" href="{{ asset('...') }}">
            //'favicon_path' => '/favicon-admin.svg',

            // the title visible above the login form (define this option only if you are
            // rendering the login template in a regular Symfony controller; when rendering
            // it from an EasyAdmin Dashboard this is automatically set as the Dashboard title)
            'page_title' => 'ACME login',

            // the string used to generate the CSRF token. If you don't define
            // this parameter, the login form won't include a CSRF token
            'csrf_token_intention' => 'authenticate',

            // the URL users are redirected to after the login (default: '/admin')
            'target_path' => $this->generateUrl('bo_home'),

            // the label displayed for the username form field (the |trans filter is applied to it)
            'username_label' => 'Your username',

            // the label displayed for the password form field (the |trans filter is applied to it)
            'password_label' => 'Your password',

            // the label displayed for the Sign In form button (the |trans filter is applied to it)
            'sign_in_label' => 'Log in',

            // the 'name' HTML attribute of the <input> used for the username field (default: '_username')
            'username_parameter' => 'my_custom_username_field',

            // the 'name' HTML attribute of the <input> used for the password field (default: '_password')
            'password_parameter' => 'my_custom_password_field',

            // whether to enable or not the "forgot password?" link (default: false)
            'forgot_password_enabled' => false,

            // the path (i.e. a relative or absolute URL) to visit when clicking the "forgot password?" link (default: '#')
            'forgot_password_path' => '',

            // the label displayed for the "forgot password?" link (the |trans filter is applied to it)
            'forgot_password_label' => 'Forgot your password?',

            // whether to enable or not the "remember me" checkbox (default: false)
            'remember_me_enabled' => false,

            // remember me name form field (default: '_remember_me')
            'remember_me_parameter' => 'custom_remember_me_param',

            // whether to check by default the "remember me" checkbox (default: false)
            'remember_me_checked' => false,

            // the label displayed for the remember me checkbox (the |trans filter is applied to it)
            'remember_me_label' => 'Remember me',
        ]);
    }







    #[Route('/user/definePassword/{hash}', name: 'user_define_password')]
    public function definePassword(?User $user, Request $request,  UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(DefinePasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('user_logout');
        }

        return $this->render('user/define_password.html.twig', [
            'definePasswordForm' => $form->createView(),
        ]);
    }

    /**
     * Envoi un mail à l'utilisateur pour qu'il puisse créer son mot de passe.
     *
     * @param BoNotification $notification
     * @return RedirectResponse
     */
    #[Route('/user/sendAccess/{hash}', name: 'user_send_access')]
    public function sendAccess(?User $user, BoNotification $notification, AdminUrlGenerator $adminUrlGenerator): RedirectResponse
    {
        // Envoi d'un mail à l'utilisateur pour qu'il puisse créer son mot de passe
        $sent = $notification->sendAcces(
            // Utilissateur sélectionné
            $user,
            // Lien pour définir le mot de passe
            $this->generateUrl(
                'user_define_password',
                ['hash' => $user->getHash()],
                UrlGeneratorInterface::ABSOLUTE_URL
            )
        );
        // Si le mail a été envoyé
        if($sent) {
            $this->addFlash('success', 'Votre email a bien été envoyé');
        }else{
            $this->addFlash('error', "Une erreur s'est produite, veuillez renouveler l'operation, si l'erreur persite contactez l'administrateur du site" );
        }
        // Retour à la liste des utilisateurs
        $url = $adminUrlGenerator
            ->setController(UserCrudController::class)
            ->setAction(Crud::PAGE_INDEX)
            ->generateUrl();
        // Redirection vers la liste des utilisateurs
        return $this->redirect($url);
    }



    #[Route(path: '/user/logout', name: 'user_logout')]
    public function logout()
    {
        # throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/user/register', name: 'user_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppCustomAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('user_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('bienvenue@e-systemes.com', 'E-SYSTEMES'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('user/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('user/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/user/verify/email', name: 'user_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('user_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Votre adresse mail a été vérifiée');

        return $this->redirectToRoute('user_register');
    }
}
