<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ApiController extends AbstractController
{
    #[Route('api/users', name: 'api_users', methods: 'GET')]
    public function getUsers(): Response
    {
        $users = [
            new User(1, 'John Doe', 'john.doe@foo.org'),
            new User(2, 'John Doe', 'john.doe@foo.org'),
            new User(3, 'John Doe', 'john.doe@foo.org'),
        ];

        return $this->json($users);
    }
}
