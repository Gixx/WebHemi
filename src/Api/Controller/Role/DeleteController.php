<?php

declare(strict_types=1);

namespace App\Api\Controller\Role;

use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/api/roles/{id<\d+>}', name: 'admin_api_role_')]
#[IsGranted('role.delete')]
class DeleteController extends AbstractController
{
    public function __construct(
        private readonly RoleRepository $roleRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('', name: 'delete', methods: ['DELETE'])]
    public function __invoke(int $id): JsonResponse
    {
        $role = $this->roleRepository->find($id);
        if (null === $role) {
            throw new NotFoundHttpException('Role not found.');
        }

        if ($role->isReadOnly()) {
            throw new BadRequestHttpException('This role is read-only and cannot be deleted.');
        }

        if ($role->getUserRoles()->count() > 0) {
            throw new BadRequestHttpException('Cannot delete a role that is assigned to users.');
        }

        $this->entityManager->remove($role);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
