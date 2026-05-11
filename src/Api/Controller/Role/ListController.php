<?php

declare(strict_types=1);

namespace App\Api\Controller\Role;

use App\Repository\RoleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/api/roles', name: 'admin_api_role_')]
#[IsGranted('role.list')]
class ListController extends AbstractController
{
    public function __construct(private readonly RoleRepository $roleRepository)
    {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $roles = $this->roleRepository->findBy([], ['name' => 'ASC']);

        return $this->json(array_map(static fn ($role) => [
            'id' => $role->getId(),
            'name' => $role->getName(),
            'label' => $role->getLabel(),
            'isReadOnly' => $role->isReadOnly(),
            'permissionCount' => $role->getPermissions()->count(),
        ], $roles));
    }
}
