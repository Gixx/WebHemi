<?php

declare(strict_types=1);

namespace App\Admin\Controller;

use App\Entity\Permission;
use App\Repository\PermissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/permissions', name: 'admin_permission_')]
final class PermissionController extends AbstractController
{
    public function __construct(
        private readonly PermissionRepository $permissionRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route(name: 'list', methods: ['GET'])]
    #[IsGranted('permission.list')]
    public function list(): Response
    {
        return $this->render('admin/permission/list.html.twig', [
            'permissions' => $this->permissionRepository->findBy([], ['name' => 'ASC']),
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    #[IsGranted('permission.create')]
    public function create(Request $request): Response
    {
        $permission = new Permission();

        if ($request->isMethod(Request::METHOD_POST)) {
            $permission
                ->setName($request->request->getString('name', ''))
                ->setLabel($request->request->getString('label', ''));

            if ('' !== $permission->getName() && '' !== $permission->getLabel()) {
                $this->entityManager->persist($permission);
                $this->entityManager->flush();
                $this->addFlash('success', 'Permission created successfully.');

                return $this->redirectToRoute('admin_permission_list');
            }

            $this->addFlash('error', 'Name and label are required.');
        }

        return $this->render('admin/permission/form.html.twig', [
            'permission' => $permission,
            'title' => 'Create Permission',
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    #[IsGranted('permission.edit')]
    public function edit(int $id, Request $request): Response
    {
        $permission = $this->permissionRepository->find($id);
        if (null === $permission) {
            throw $this->createNotFoundException('Permission not found.');
        }

        if ($request->isMethod(Request::METHOD_POST)) {
            $permission
                ->setName($request->request->getString('name', ''))
                ->setLabel($request->request->getString('label', ''));

            $this->entityManager->flush();
            $this->addFlash('success', 'Permission updated successfully.');

            return $this->redirectToRoute('admin_permission_list');
        }

        return $this->render('admin/permission/form.html.twig', [
            'permission' => $permission,
            'title' => 'Edit Permission',
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    #[IsGranted('permission.delete')]
    public function delete(int $id): Response
    {
        $permission = $this->permissionRepository->find($id);
        if (null === $permission) {
            throw $this->createNotFoundException('Permission not found.');
        }

        $this->entityManager->remove($permission);
        $this->entityManager->flush();
        $this->addFlash('success', 'Permission deleted successfully.');

        return $this->redirectToRoute('admin_permission_list');
    }
}
