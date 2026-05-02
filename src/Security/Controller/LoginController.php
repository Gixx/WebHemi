<?php

declare(strict_types=1);

namespace App\Security\Controller;

use App\Security\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function __invoke(
        AuthenticationUtils $authenticationUtils,
        FormFactoryInterface $formFactory,
        CsrfTokenManagerInterface $csrfTokenManager,
    ): Response {
        if ($this->getUser() instanceof \Symfony\Component\Security\Core\User\UserInterface) {
            return $this->redirectToRoute('admin_dashboard');
        }

        $form = $formFactory->createNamed('', LoginType::class, [
            '_username' => $authenticationUtils->getLastUsername(),
            '_password' => '',
            '_csrf_token' => $csrfTokenManager->getToken('authenticate')->getValue(),
        ], [
            'action' => $this->generateUrl('app_login'),
            'method' => 'POST',
            'csrf_token' => $csrfTokenManager->getToken('authenticate')->getValue(),
        ]);

        return $this->render('security/login.html.twig', [
            'loginForm' => $form,
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }
}
