<?php

declare(strict_types=1);

namespace App\Security\Controller;

use App\Security\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function __invoke(
        AuthenticationUtils $authenticationUtils,
        FormFactoryInterface $formFactory,
    ): Response {
        if ($this->getUser() instanceof \Symfony\Component\Security\Core\User\UserInterface) {
            return $this->redirectToRoute('admin_dashboard');
        }

        $form = $formFactory->createNamed('', LoginType::class, [
            '_username' => $authenticationUtils->getLastUsername(),
            '_password' => '',
        ], [
            'action' => $this->generateUrl('app_login'),
            'method' => 'POST',
        ]);

        return $this->render('security/login.html.twig', [
            'loginForm' => $form,
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }
}
