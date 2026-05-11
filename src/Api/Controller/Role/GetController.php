<?php

declare(strict_types=1);

namespace App\Api\Controller\Role;

use App\Repository\RoleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/api/roles/{id<\d+>}', name: 'admin_api_role_')]
#[IsGranted('role.view')]
class GetController extends AbstractController
{
    public function __construct(private readonly RoleRepository $roleRepository)
    {
    }

    #[Route('', name: 'view', methods: ['GET'])]
    public function __invoke(int $id): JsonResponse
    {
        $role = $this->roleRepository->find($id);
        if (null === $role) {
            throw new NotFoundHttpException('Role not found.');
        }

        return $this->json([
            'id' => $role->getId(),
            'name' => $role->getName(),
            'label' => $role->getLabel(),
            'isReadOnly' => $role->isReadOnly(),
            'permissions' => array_map(static fn ($p) => [
                'id' => $p->getId(),
                'name' => $p->getName(),
                'label' => $p->getLabel(),
            ], $role->getPermissions()->toArray()),
        ]);
    }
}
