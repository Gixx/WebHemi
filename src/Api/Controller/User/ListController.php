<?php

declare(strict_types=1);

namespace App\Api\Controller\User;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/api/users', name: 'admin_api_user_')]
#[IsGranted('user.list')]
class ListController extends AbstractController
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $users = $this->userRepository->findBy([], ['email' => 'ASC']);

        return $this->json(array_map(static fn ($user) => [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'avatarType' => $user->getAvatarType(),
            'roles' => $user->getRoles(),
        ], $users));
    }
}
