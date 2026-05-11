<?php

declare(strict_types=1);

namespace App\Api\Controller\SiteAssignment;

use App\Entity\SiteAssignment;
use App\Repository\RoleRepository;
use App\Repository\SiteAssignmentRepository;
use App\Repository\SiteRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/api/users/{userId<\d+>}/assignments', name: 'admin_api_user_assignment_')]
#[IsGranted('ROLE_ADMIN')]
class PostController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly SiteRepository $siteRepository,
        private readonly RoleRepository $roleRepository,
        private readonly SiteAssignmentRepository $siteAssignmentRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function __invoke(int $userId, Request $request): JsonResponse
    {
        $user = $this->userRepository->find($userId);
        if (null === $user) {
            throw new NotFoundHttpException('User not found.');
        }

        $data = $request->toArray();
        $site = $this->siteRepository->find((int) ($data['siteId'] ?? 0));
        $role = $this->roleRepository->find((int) ($data['roleId'] ?? 0));

        if (null === $site || null === $role) {
            throw new BadRequestHttpException('Invalid site or role.');
        }

        if (null !== $this->siteAssignmentRepository->findOneBy(['user' => $user, 'site' => $site])) {
            throw new BadRequestHttpException(sprintf('User already has an assignment for "%s".', $site->getName()));
        }

        $assignment = (new SiteAssignment())
            ->setUser($user)
            ->setSite($site)
            ->setRole($role);

        $this->entityManager->persist($assignment);
        $this->entityManager->flush();

        return $this->json(['id' => $assignment->getId()], Response::HTTP_CREATED);
    }
}
