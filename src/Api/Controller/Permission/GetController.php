<?php

declare(strict_types=1);

namespace App\Api\Controller\Permission;

use App\Repository\PermissionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/api/permissions/{id<\d+>}', name: 'admin_api_permission_')]
#[IsGranted('permission.view')]
class GetController extends AbstractController
{
    public function __construct(private readonly PermissionRepository $permissionRepository)
    {
    }

    #[Route('', name: 'view', methods: ['GET'])]
    public function __invoke(int $id): JsonResponse
    {
        $permission = $this->permissionRepository->find($id);
        if (null === $permission) {
            throw new NotFoundHttpException('Permission not found.');
        }

        return $this->json([
            'id' => $permission->getId(),
            'name' => $permission->getName(),
            'label' => $permission->getLabel(),
            'isReadOnly' => $permission->isReadOnly(),
            'roles' => array_map(static fn ($r) => [
                'id' => $r->getId(),
                'name' => $r->getName(),
                'label' => $r->getLabel(),
            ], $permission->getRoles()->toArray()),
        ]);
    }
}
