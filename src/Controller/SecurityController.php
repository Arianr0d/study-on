<?php

namespace App\Controller;

use App\DTO\CredUserDTO;
use App\Exception\BillingUnavailableException;
use App\Security\UserAuthenticator;
use App\Service\BillingClient;
use App\Form\RegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(
        Request $request,
        UserAuthenticatorInterface $authenticator,
        UserAuthenticator $userAuthenticator,
        BillingClient $billingClient): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_profile');
        }
        $credentials = new CredUserDTO();
        $form = $this->createForm(RegisterType::class, $credentials);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $user = $billingClient->userRegister($credentials);
            } catch (BillingUnavailableException $e) {
                return $this->render('security/register.html.twig', [
                    'registrationForm' => $form->createView(),
                    'errors' => $e->getMessage(),
                ]);
            }
            return $authenticator->authenticateUser(
                $user,
                $userAuthenticator,
                $request
            );
        }
        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
