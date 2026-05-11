<?php

declare(strict_types=1);

namespace App\Api\Controller\SiteAssignment;

use App\Repository\SiteAssignmentRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/api/users/{userId<\d+>}/assignments', name: 'admin_api_user_assignment_')]
#[IsGranted('ROLE_ADMIN')]
class ListController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly SiteAssignmentRepository $siteAssignmentRepository,
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function __invoke(int $userId): JsonResponse
    {
        $user = $this->userRepository->find($userId);
        if (null === $user) {
            throw new NotFoundHttpException('User not found.');
        }

        $assignments = $this->siteAssignmentRepository->findBy(['user' => $user]);

        return $this->json(array_map(static fn ($a) => [
            'id' => $a->getId(),
            'siteId' => $a->getSite()->getId(),
            'siteName' => $a->getSite()->getName(),
            'roleId' => $a->getRole()->getId(),
            'roleName' => $a->getRole()->getName(),
        ], $assignments));
    }
}
