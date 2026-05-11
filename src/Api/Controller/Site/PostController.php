<?php

declare(strict_types=1);

namespace App\Api\Controller\Site;

use App\Entity\Site;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/api/sites', name: 'admin_api_site_')]
#[IsGranted('ROLE_ADMIN')]
class PostController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->toArray();
        $slug = trim((string) ($data['slug'] ?? ''));
        $name = trim((string) ($data['name'] ?? ''));

        if ('' === $slug || '' === $name) {
            throw new BadRequestHttpException('Slug and name are required.');
        }

        $site = (new Site())
            ->setSlug($slug)
            ->setName($name)
            ->setIsEnabled((bool) ($data['isEnabled'] ?? true));

        $this->entityManager->persist($site);
        $this->entityManager->flush();

        return $this->json(['id' => $site->getId()], Response::HTTP_CREATED);
    }
}
