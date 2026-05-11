<?php

declare(strict_types=1);

namespace App\Api\Controller\Role;

use App\Entity\Role;
use App\Repository\PermissionRepository;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/api/roles/{id<\d+>}', name: 'admin_api_role_')]
#[IsGranted('role.edit')]
class PutController extends AbstractController
{
    public function __construct(
        private readonly RoleRepository $roleRepository,
        private readonly PermissionRepository $permissionRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('', name: 'update', methods: ['PUT'])]
    public function __invoke(int $id, Request $request): JsonResponse
    {
        $role = $this->roleRepository->find($id);
        if (null === $role) {
            throw new NotFoundHttpException('Role not found.');
        }

        if ($role->isReadOnly()) {
            throw new BadRequestHttpException('This role is read-only and cannot be edited.');
        }

        $data = $request->toArray();

        if (isset($data['name'])) {
            $role->setName(trim((string) $data['name']));
        }

        if (isset($data['label'])) {
            $role->setLabel(trim((string) $data['label']));
        }

        if (isset($data['permissions'])) {
            $this->syncPermissions($role, (array) $data['permissions']);
        }

        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    /** @param array<mixed> $permissionIds */
    private function syncPermissions(Role $role, array $permissionIds): void
    {
        foreach ($role->getPermissions()->toArray() as $existing) {
            $role->removePermission($existing);
        }

        foreach ($permissionIds as $permissionId) {
            $permission = $this->permissionRepository->find((int) $permissionId);
            if (null !== $permission) {
                $role->addPermission($permission);
            }
        }
    }
}
