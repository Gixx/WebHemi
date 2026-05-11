<?php

declare(strict_types=1);

namespace App\Api\Controller\Site;

use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/api/sites/{id<\d+>}', name: 'admin_api_site_')]
#[IsGranted('ROLE_ADMIN')]
class DeleteController extends AbstractController
{
    public function __construct(
        private readonly SiteRepository $siteRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('', name: 'delete', methods: ['DELETE'])]
    public function __invoke(int $id): JsonResponse
    {
        $site = $this->siteRepository->find($id);
        if (null === $site) {
            throw new NotFoundHttpException('Site not found.');
        }

        $this->entityManager->remove($site);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
