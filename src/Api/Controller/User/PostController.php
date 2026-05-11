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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/api/users', name: 'admin_api_user_')]
#[IsGranted('user.create')]
class PostController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly RoleRepository $roleRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->toArray();
        $email = trim((string) ($data['email'] ?? ''));
        $password = trim((string) ($data['password'] ?? ''));
        $avatarType = trim((string) ($data['avatarType'] ?? User::AVATAR_TYPE_DEFAULT));

        if ('' === $email || '' === $password) {
            throw new BadRequestHttpException('Email and password are required.');
        }

        if (null !== $this->userRepository->findOneBy(['email' => $email])) {
            throw new BadRequestHttpException('Email is already in use.');
        }

        $user = (new User())
            ->setEmail($email)
            ->setAvatarType($avatarType);
        $user->setPasswordHash($this->passwordHasher->hashPassword($user, $password));

        foreach ((array) ($data['roles'] ?? []) as $roleId) {
            $role = $this->roleRepository->find((int) $roleId);
            if (null !== $role) {
                $user->addRole($role);
            }
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json(['id' => $user->getId()], Response::HTTP_CREATED);
    }
}
