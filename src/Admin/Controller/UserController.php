<?php

declare(strict_types=1);

namespace App\Admin\Controller;

use App\Entity\User;
use App\Repository\RoleRepository;
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
                $this->addFlash('error', 'Email and password are required.');
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

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    #[IsGranted('user.edit')]
    public function edit(int $id, Request $request): Response
    {
        $user = $this->userRepository->find($id);
        if (null === $user) {
            throw $this->createNotFoundException('User not found.');
        }

        if ($request->isMethod(Request::METHOD_POST)) {
            $email = $request->request->getString('email', '');
            $password = $request->request->getString('password', '');
            $avatarType = $request->request->getString('avatarType', User::AVATAR_TYPE_DEFAULT);

            if ('' !== $email) {
                $user->setEmail($email);
            }

            if ('' !== $password) {
                $user->setPasswordHash($this->passwordHasher->hashPassword($user, $password));
            }

            $user->setAvatarType($avatarType);
            $this->syncRoles($user, $request->request->all('roles'));
            $this->entityManager->flush();
            $this->addFlash('success', 'User updated successfully.');

            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('admin/user/form.html.twig', [
            'user' => $user,
            'allRoles' => $this->roleRepository->findBy([], ['name' => 'ASC']),
            'title' => 'Edit User',
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    #[IsGranted('user.delete')]
    public function delete(int $id): Response
    {
        $user = $this->userRepository->find($id);
        if (null === $user) {
            throw $this->createNotFoundException('User not found.');
        }

        if ($this->getUser() instanceof User && $this->getUser()->getId() === $user->getId()) {
            $this->addFlash('error', 'You cannot delete your own account.');

            return $this->redirectToRoute('admin_user_list');
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();
        $this->addFlash('success', 'User deleted successfully.');

        return $this->redirectToRoute('admin_user_list');
    }

    /** @param array<int, mixed> $roleIds */
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
