<?php

declare(strict_types=1);

namespace App\Admin\Controller;

use App\Repository\PermissionRepository;
use App\Repository\RoleRepository;
use App\Repository\SiteRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/desktop/window', name: 'admin_desktop_window_')]
final class DesktopWindowController extends AbstractController
{
    public function __construct(
        private readonly SiteRepository $siteRepository,
        private readonly PermissionRepository $permissionRepository,
        private readonly RoleRepository $roleRepository,
        private readonly UserRepository $userRepository,
    ) {
    }

    #[Route('/control-panel', name: 'control_panel', methods: ['GET'])]
    #[IsGranted('dashboard.view')]
    public function controlPanel(): Response
    {
        return $this->render('admin/desktop/window/control_panel.html.twig');
    }

    #[Route('/sites', name: 'sites', methods: ['GET'])]
    #[IsGranted('site.list')]
    public function sites(): Response
    {
        $rows = [];

        foreach ($this->siteRepository->findBy([], ['name' => 'ASC']) as $site) {
            $siteId = $site->getId();
            if (null === $siteId) {
                continue;
            }

            $rows[] = [
                'name' => $site->getName(),
                'url' => $this->generateUrl('admin_site_show', ['id' => $siteId]),
            ];
        }

        return $this->render('admin/desktop/window/module_list.html.twig', [
            'title' => 'Sites',
            'rows' => $rows,
            'listUrl' => $this->generateUrl('admin_site_list'),
            'emptyMessage' => 'No sites available.',
        ]);
    }

    #[Route('/permissions', name: 'permissions', methods: ['GET'])]
    #[IsGranted('permission.list')]
    public function permissions(): Response
    {
        $rows = [];

        foreach ($this->permissionRepository->findBy([], ['name' => 'ASC']) as $permission) {
            $permissionId = $permission->getId();
            if (null === $permissionId) {
                continue;
            }

            $rows[] = [
                'name' => sprintf('%s (%s)', $permission->getLabel(), $permission->getName()),
                'url' => $this->generateUrl('admin_permission_show', ['id' => $permissionId]),
            ];
        }

        return $this->render('admin/desktop/window/module_list.html.twig', [
            'title' => 'Permissions',
            'rows' => $rows,
            'listUrl' => $this->generateUrl('admin_permission_list'),
            'emptyMessage' => 'No permissions available.',
        ]);
    }

    #[Route('/roles', name: 'roles', methods: ['GET'])]
    #[IsGranted('role.list')]
    public function roles(): Response
    {
        $rows = [];

        foreach ($this->roleRepository->findBy([], ['name' => 'ASC']) as $role) {
            $roleId = $role->getId();
            if (null === $roleId) {
                continue;
            }

            $rows[] = [
                'name' => sprintf('%s (%s)', $role->getLabel(), $role->getName()),
                'url' => $this->generateUrl('admin_role_show', ['id' => $roleId]),
            ];
        }

        return $this->render('admin/desktop/window/module_list.html.twig', [
            'title' => 'Roles',
            'rows' => $rows,
            'listUrl' => $this->generateUrl('admin_role_list'),
            'emptyMessage' => 'No roles available.',
        ]);
    }

    #[Route('/users', name: 'users', methods: ['GET'])]
    #[IsGranted('user.list')]
    public function users(): Response
    {
        $rows = [];

        foreach ($this->userRepository->findBy([], ['email' => 'ASC']) as $user) {
            $userId = $user->getId();
            if (null === $userId) {
                continue;
            }

            $rows[] = [
                'name' => $user->getEmail(),
                'url' => $this->generateUrl('admin_user_show', ['id' => $userId]),
            ];
        }

        return $this->render('admin/desktop/window/module_list.html.twig', [
            'title' => 'Users',
            'rows' => $rows,
            'listUrl' => $this->generateUrl('admin_user_list'),
            'emptyMessage' => 'No users available.',
        ]);
    }

    #[Route('/site-workspace/{id<\d+>}', name: 'site_workspace', methods: ['GET'])]
    #[IsGranted('site.edit')]
    public function siteWorkspace(int $id): Response
    {
        $site = $this->siteRepository->find($id);
        if (null === $site) {
            throw $this->createNotFoundException('Site not found.');
        }

        return $this->render('admin/desktop/window/site_workspace.html.twig', [
            'site' => $site,
        ]);
    }
}
