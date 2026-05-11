<?php

declare(strict_types=1);

namespace App\Api\Controller\Permission;

use App\Repository\PermissionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/api/permissions', name: 'admin_api_permission_')]
#[IsGranted('permission.list')]
class ListController extends AbstractController
{
    public function __construct(private readonly PermissionRepository $permissionRepository)
    {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $permissions = $this->permissionRepository->findBy([], ['name' => 'ASC']);

        return $this->json(array_map(static fn ($permission) => [
            'id' => $permission->getId(),
            'name' => $permission->getName(),
            'label' => $permission->getLabel(),
            'isReadOnly' => $permission->isReadOnly(),
        ], $permissions));
    }
}
