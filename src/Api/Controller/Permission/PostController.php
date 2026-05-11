<?php

declare(strict_types=1);

namespace App\Api\Controller\Permission;

use App\Entity\Permission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/api/permissions', name: 'admin_api_permission_')]
#[IsGranted('permission.create')]
class PostController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
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

        $permission = (new Permission())->setName($name)->setLabel($label);

        $this->entityManager->persist($permission);
        $this->entityManager->flush();

        return $this->json(['id' => $permission->getId()], Response::HTTP_CREATED);
    }
}
