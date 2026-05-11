<?php

declare(strict_types=1);

namespace App\Api\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class LoginController extends AbstractController
{
    #[Route('/admin/api/login', name: 'admin_api_login', methods: ['POST'])]
    public function __invoke(): JsonResponse
    {
        // Intercepted by json_login firewall authenticator — never reached.
        throw new \LogicException('This route is handled by the security firewall.');
    }
}
