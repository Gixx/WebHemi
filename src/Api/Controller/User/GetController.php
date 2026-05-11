<?php

declare(strict_types=1);

namespace App\Api\Controller\User;

use App\Repository\SiteAssignmentRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/api/users/{id<\d+>}', name: 'admin_api_user_')]
#[IsGranted('user.view')]
class GetController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly SiteAssignmentRepository $siteAssignmentRepository,
    ) {
    }

    #[Route('', name: 'view', methods: ['GET'])]
    public function __invoke(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);
        if (null === $user) {
            throw new NotFoundHttpException('User not found.');
        }

        $assignments = $this->siteAssignmentRepository->findBy(['user' => $user]);

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'avatarType' => $user->getAvatarType(),
            'avatarUrl' => $user->getAvatarUrl(),
            'roles' => $user->getRoles(),
            'siteAssignments' => array_map(static fn ($a) => [
                'id' => $a->getId(),
                'siteId' => $a->getSite()->getId(),
                'siteName' => $a->getSite()->getName(),
                'roleId' => $a->getRole()->getId(),
                'roleName' => $a->getRole()->getName(),
            ], $assignments),
        ]);
    }
}
