<?php

declare(strict_types=1);

namespace App\Api\Controller\User;

use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/api/users/{id<\d+>}', name: 'admin_api_user_')]
#[IsGranted('user.edit')]
class PutController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly RoleRepository $roleRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    #[Route('', name: 'update', methods: ['PUT'])]
    public function __invoke(int $id, Request $request): JsonResponse
    {
        $user = $this->userRepository->find($id);
        if (null === $user) {
            throw new NotFoundHttpException('User not found.');
        }

        $currentUser = $this->getUser();
        $isAdmin = $currentUser instanceof User && $currentUser->hasRole('ROLE_ADMIN');

        if (!$isAdmin && (!$currentUser instanceof User || $currentUser->getId() !== $user->getId())) {
            throw new BadRequestHttpException('You can only edit your own profile.');
        }

        $data = $request->toArray();

        if ($isAdmin && isset($data['email'])) {
            $email = trim((string) $data['email']);
            if ('' !== $email) {
                $user->setEmail($email);
            }
            if (isset($data['roles'])) {
                $this->syncRoles($user, (array) $data['roles']);
            }
        }

        if (isset($data['password'])) {
            $password = trim((string) $data['password']);
            if ('' !== $password) {
                $user->setPasswordHash($this->passwordHasher->hashPassword($user, $password));
            }
        }

        if (isset($data['avatarType'])) {
            $user->setAvatarType(trim((string) $data['avatarType']));
        }

        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
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
