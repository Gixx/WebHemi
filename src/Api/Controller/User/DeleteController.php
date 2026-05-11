<?php

declare(strict_types=1);

namespace App\Api\Controller\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/api/users/{id<\d+>}', name: 'admin_api_user_')]
#[IsGranted('user.delete')]
class DeleteController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('', name: 'delete', methods: ['DELETE'])]
    public function __invoke(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);
        if (null === $user) {
            throw new NotFoundHttpException('User not found.');
        }

        $currentUser = $this->getUser();

        if ($currentUser instanceof User && $currentUser->getId() === $user->getId()) {
            throw new BadRequestHttpException('You cannot delete your own account.');
        }

        if ($user->hasRole('ROLE_ADMIN') && (!$currentUser instanceof User || !$currentUser->hasRole('ROLE_ADMIN'))) {
            throw new BadRequestHttpException('Only ROLE_ADMIN users can delete a ROLE_ADMIN user.');
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
