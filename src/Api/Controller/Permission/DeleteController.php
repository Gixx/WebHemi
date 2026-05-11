<?php

declare(strict_types=1);

namespace App\Api\Controller\Permission;

use App\Repository\PermissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/api/permissions/{id<\d+>}', name: 'admin_api_permission_')]
#[IsGranted('permission.delete')]
class DeleteController extends AbstractController
{
    public function __construct(
        private readonly PermissionRepository $permissionRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('', name: 'delete', methods: ['DELETE'])]
    public function __invoke(int $id): JsonResponse
    {
        $permission = $this->permissionRepository->find($id);
        if (null === $permission) {
            throw new NotFoundHttpException('Permission not found.');
        }

        if ($permission->isReadOnly()) {
            throw new BadRequestHttpException('This permission is read-only and cannot be deleted.');
        }

        $this->entityManager->remove($permission);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
