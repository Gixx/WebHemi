<?php

declare(strict_types=1);

namespace App\Admin\Controller;

use App\Entity\Role;
use App\Repository\PermissionRepository;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/roles', name: 'admin_role_')]
final class RoleController extends AbstractController
{
    public function __construct(
        private readonly RoleRepository $roleRepository,
        private readonly PermissionRepository $permissionRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route(name: 'list', methods: ['GET'])]
    #[IsGranted('role.list')]
    public function list(): Response
    {
        return $this->render('admin/role/list.html.twig', [
            'roles' => $this->roleRepository->findBy([], ['name' => 'ASC']),
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    #[IsGranted('role.create')]
    public function create(Request $request): Response
    {
        $role = new Role();

        if ($request->isMethod(Request::METHOD_POST)) {
            $role
                ->setName($request->request->getString('name', ''))
                ->setLabel($request->request->getString('label', ''));

            if ('' !== $role->getName() && '' !== $role->getLabel()) {
                $this->entityManager->persist($role);
                $this->syncPermissions($role, $request->request->all('permissions'));
                $this->entityManager->flush();
                $this->addFlash('success', 'Role created successfully.');

                return $this->redirectToRoute('admin_role_list');
            }

            $this->addFlash('error', 'Name and label are required.');
        }

        return $this->render('admin/role/form.html.twig', [
            'role' => $role,
            'allPermissions' => $this->permissionRepository->findBy([], ['name' => 'ASC']),
            'title' => 'Create Role',
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    #[IsGranted('role.edit')]
    public function edit(int $id, Request $request): Response
    {
        $role = $this->roleRepository->find($id);
        if (null === $role) {
            throw $this->createNotFoundException('Role not found.');
        }

        if ($request->isMethod(Request::METHOD_POST)) {
            $role
                ->setName($request->request->getString('name', ''))
                ->setLabel($request->request->getString('label', ''));

            $this->syncPermissions($role, $request->request->all('permissions'));
            $this->entityManager->flush();
            $this->addFlash('success', 'Role updated successfully.');

            return $this->redirectToRoute('admin_role_list');
        }

        return $this->render('admin/role/form.html.twig', [
            'role' => $role,
            'allPermissions' => $this->permissionRepository->findBy([], ['name' => 'ASC']),
            'title' => 'Edit Role',
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    #[IsGranted('role.delete')]
    public function delete(int $id): Response
    {
        $role = $this->roleRepository->find($id);
        if (null === $role) {
            throw $this->createNotFoundException('Role not found.');
        }

        if ($role->getUserRoles()->count() > 0) {
            $this->addFlash('error', 'Cannot delete a role that is assigned to users.');

            return $this->redirectToRoute('admin_role_list');
        }

        $this->entityManager->remove($role);
        $this->entityManager->flush();
        $this->addFlash('success', 'Role deleted successfully.');

        return $this->redirectToRoute('admin_role_list');
    }

    /** @param array<int, mixed> $permissionIds */
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
