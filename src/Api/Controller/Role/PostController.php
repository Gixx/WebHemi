<?php

declare(strict_types=1);

namespace App\Api\Controller\Role;

use App\Entity\Role;
use App\Repository\PermissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/api/roles', name: 'admin_api_role_')]
#[IsGranted('role.create')]
class PostController extends AbstractController
{
    public function __construct(
        private readonly PermissionRepository $permissionRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->toArray();
        $name = trim((string) ($data['name'] ?? ''));
        $label = trim((string) ($data['label'] ?? ''));

        if ('' === $name || '' === $label) {
            throw new BadRequestHttpException('Name and label are required.');
        }

        $role = (new Role())->setName($name)->setLabel($label);

        foreach ((array) ($data['permissions'] ?? []) as $permissionId) {
            $permission = $this->permissionRepository->find((int) $permissionId);
            if (null !== $permission) {
                $role->addPermission($permission);
            }
        }

        $this->entityManager->persist($role);
        $this->entityManager->flush();

        return $this->json(['id' => $role->getId()], Response::HTTP_CREATED);
    }
}
