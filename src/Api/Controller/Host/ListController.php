<?php

declare(strict_types=1);

namespace App\Api\Controller\Host;

use App\Repository\SiteHostRepository;
use App\Repository\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/api/sites/{siteId<\d+>}/hosts', name: 'admin_api_site_host_')]
#[IsGranted('site.edit')]
#[IsGranted('site.own', subject: 'siteId')]
class ListController extends AbstractController
{
    public function __construct(
        private readonly SiteRepository $siteRepository,
        private readonly SiteHostRepository $siteHostRepository,
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function __invoke(int $siteId): JsonResponse
    {
        $site = $this->siteRepository->find($siteId);
        if (null === $site) {
            throw new NotFoundHttpException('Site not found.');
        }

        $hosts = $this->siteHostRepository->findBy(['site' => $site]);

        return $this->json(array_map(static fn ($host) => [
            'id' => $host->getId(),
            'host' => $host->getHost(),
            'surface' => $host->getSurface()->value,
            'status' => $host->getStatus(),
            'isActive' => $host->isActive(),
        ], $hosts));
    }
}
