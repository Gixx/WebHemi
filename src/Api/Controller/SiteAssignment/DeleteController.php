<?php

declare(strict_types=1);

namespace App\Api\Controller\SiteAssignment;

use App\Repository\SiteAssignmentRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/api/users/{userId<\d+>}/assignments/{assignmentId<\d+>}', name: 'admin_api_user_assignment_')]
#[IsGranted('ROLE_ADMIN')]
class DeleteController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly SiteAssignmentRepository $siteAssignmentRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('', name: 'delete', methods: ['DELETE'])]
    public function __invoke(int $userId, int $assignmentId): JsonResponse
    {
        $user = $this->userRepository->find($userId);
        if (null === $user) {
            throw new NotFoundHttpException('User not found.');
        }

        $assignment = $this->siteAssignmentRepository->find($assignmentId);
        if (null === $assignment || $assignment->getUser()->getId() !== $userId) {
            throw new NotFoundHttpException('Assignment not found.');
        }

        $this->entityManager->remove($assignment);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
