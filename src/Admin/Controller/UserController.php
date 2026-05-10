<?php

declare(strict_types=1);

namespace App\Admin\Controller;

use App\Entity\SiteAssignment;
use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\SiteAssignmentRepository;
use App\Repository\SiteRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/users', name: 'admin_user_')]
final class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly RoleRepository $roleRepository,
        private readonly SiteRepository $siteRepository,
        private readonly SiteAssignmentRepository $siteAssignmentRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    #[Route(name: 'list', methods: ['GET'])]
    #[IsGranted('user.list')]
    public function list(): Response
    {
        return $this->render('admin/user/list.html.twig', [
            'users' => $this->userRepository->findBy([], ['email' => 'ASC']),
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    #[IsGranted('user.create')]
    public function create(Request $request): Response
    {
        $user = new User();

        if ($request->isMethod(Request::METHOD_POST)) {
            $email = $request->request->getString('email', '');
            $password = $request->request->getString('password', '');
            $avatarType = $request->request->getString('avatarType', User::AVATAR_TYPE_DEFAULT);

            if ('' === $email || '' === $password) {
                $this->addFlash('failed', 'Email and password are required.');
            } else {
                $user->setEmail($email)
                    ->setPasswordHash($this->passwordHasher->hashPassword($user, $password))
                    ->setAvatarType($avatarType);

                $this->entityManager->persist($user);
                $this->syncRoles($user, $request->request->all('roles'));
                $this->entityManager->flush();
                $this->addFlash('success', 'User created successfully.');

                return $this->redirectToRoute('admin_user_list');
            }
        }

        return $this->render('admin/user/form.html.twig', [
            'user' => $user,
            'allRoles' => $this->roleRepository->findBy([], ['name' => 'ASC']),
            'title' => 'Create User',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'show', methods: ['GET'])]
    #[IsGranted('user.view')]
    public function show(int $id): Response
    {
        $user = $this->userRepository->find($id);
        if (null === $user) {
            throw $this->createNotFoundException('User not found.');
        }

        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
            'siteAssignments' => $this->siteAssignmentRepository->findBy(['user' => $user]),
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'edit', methods: ['GET', 'POST'])]
    #[IsGranted('user.edit')]
    public function edit(int $id, Request $request): Response
    {
        $user = $this->userRepository->find($id);
        if (null === $user) {
            throw $this->createNotFoundException('User not found.');
        }

        $currentUser = $this->getUser();
        $isAdmin = $currentUser instanceof User && $currentUser->hasRole('ROLE_ADMIN');

        if (!$isAdmin && (!$currentUser instanceof User || $currentUser->getId() !== $user->getId())) {
            $this->addFlash('warning', 'You can only edit your own profile.');

            return $this->redirectToRoute('admin_user_list');
        }

        if ($request->isMethod(Request::METHOD_POST)) {
            $password = $request->request->getString('password', '');
            $avatarType = $request->request->getString('avatarType', User::AVATAR_TYPE_DEFAULT);

            if ($isAdmin) {
                $email = $request->request->getString('email', '');
                if ('' !== $email) {
                    $user->setEmail($email);
                }
                $this->syncRoles($user, $request->request->all('roles'));
            }

            if ('' !== $password) {
                $user->setPasswordHash($this->passwordHasher->hashPassword($user, $password));
            }

            $user->setAvatarType($avatarType);
            $this->entityManager->flush();
            $this->addFlash('success', 'User updated successfully.');

            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('admin/user/form.html.twig', [
            'user' => $user,
            'allRoles' => $this->roleRepository->findBy([], ['name' => 'ASC']),
            'title' => 'Edit User',
            'isAdmin' => $isAdmin,
        ]);
    }

    #[Route('/{id<\d+>}/delete', name: 'delete', methods: ['POST'])]
    #[IsGranted('user.delete')]
    public function delete(int $id): Response
    {
        $user = $this->userRepository->find($id);
        if (null === $user) {
            throw $this->createNotFoundException('User not found.');
        }

        $currentUser = $this->getUser();

        if ($currentUser instanceof User && $currentUser->getId() === $user->getId()) {
            $this->addFlash('warning', 'You cannot delete your own account.');

            return $this->redirectToRoute('admin_user_list');
        }

        if ($user->hasRole('ROLE_ADMIN') && (!$currentUser instanceof User || !$currentUser->hasRole('ROLE_ADMIN'))) {
            $this->addFlash('warning', 'Only ROLE_ADMIN users can delete a ROLE_ADMIN user.');

            return $this->redirectToRoute('admin_user_list');
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();
        $this->addFlash('success', 'User deleted successfully.');

        return $this->redirectToRoute('admin_user_list');
    }

    #[Route('/{id<\d+>}/assignments', name: 'assignments', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function assignments(int $id, Request $request): Response
    {
        $user = $this->userRepository->find($id);
        if (null === $user) {
            throw $this->createNotFoundException('User not found.');
        }

        if ($request->isMethod(Request::METHOD_POST)) {
            $siteId = $request->request->getInt('site_id');
            $roleId = $request->request->getInt('role_id');

            $site = $this->siteRepository->find($siteId);
            $role = $this->roleRepository->find($roleId);

            if (null === $site || null === $role) {
                $this->addFlash('failed', 'Invalid site or role.');
            } elseif (null !== $this->siteAssignmentRepository->findOneBy(['user' => $user, 'site' => $site])) {
                $this->addFlash('failed', sprintf('User already has an assignment for "%s".', $site->getName()));
            } else {
                $assignment = (new SiteAssignment())
                    ->setUser($user)
                    ->setSite($site)
                    ->setRole($role);

                $this->entityManager->persist($assignment);
                $this->entityManager->flush();
                $this->addFlash('success', sprintf('Assigned to "%s" as %s.', $site->getName(), $role->getName()));
            }

            return $this->redirectToRoute('admin_user_assignments', ['id' => $id]);
        }

        return $this->render('admin/user/assignments.html.twig', [
            'user' => $user,
            'assignments' => $this->siteAssignmentRepository->findBy(['user' => $user]),
            'availableSites' => $this->siteRepository->findBy([], ['name' => 'ASC']),
            'availableRoles' => $this->roleRepository->findBy([], ['name' => 'ASC']),
        ]);
    }

    #[Route('/{id<\d+>}/assignments/{assignmentId<\d+>}/remove', name: 'assignment_remove', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function removeAssignment(int $id, int $assignmentId): Response
    {
        $user = $this->userRepository->find($id);
        if (null === $user) {
            throw $this->createNotFoundException('User not found.');
        }

        $assignment = $this->siteAssignmentRepository->find($assignmentId);
        if (null === $assignment || $assignment->getUser()->getId() !== $id) {
            throw $this->createNotFoundException('Assignment not found.');
        }

        $this->entityManager->remove($assignment);
        $this->entityManager->flush();
        $this->addFlash('success', sprintf('Removed assignment for "%s".', $assignment->getSite()->getName()));

        return $this->redirectToRoute('admin_user_assignments', ['id' => $id]);
    }

    /** @param array<mixed> $roleIds */
    private function syncRoles(User $user, array $roleIds): void
    {
        foreach ($user->getRoleEntities()->toArray() as $existing) {
            $user->removeRole($existing);
        }

        foreach ($roleIds as $roleId) {
            $role = $this->roleRepository->find((int) $roleId);
            if (null !== $role) {
                $user->addRole($role);
            }
        }
    }
}
