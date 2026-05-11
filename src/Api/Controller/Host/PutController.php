<?php

declare(strict_types=1);

namespace App\Api\Controller\Host;

use App\Repository\SiteHostRepository;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/api/sites/{siteId<\d+>}/hosts/{hostId<\d+>}', name: 'admin_api_site_host_')]
#[IsGranted('site.edit')]
#[IsGranted('site.own', subject: 'siteId')]
class PutController extends AbstractController
{
    public function __construct(
        private readonly SiteRepository $siteRepository,
        private readonly SiteHostRepository $siteHostRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('', name: 'update', methods: ['PUT'])]
    public function __invoke(int $siteId, int $hostId, Request $request): JsonResponse
    {
        $site = $this->siteRepository->find($siteId);
        if (null === $site) {
            throw new NotFoundHttpException('Site not found.');
        }

        $host = $this->siteHostRepository->find($hostId);
        if (null === $host || $host->getSite()->getId() !== $siteId) {
            throw new NotFoundHttpException('Host not found.');
        }

        $data = $request->toArray();

        if (isset($data['status'])) {
            $host->setStatus((string) $data['status']);
        }

        if (isset($data['isActive'])) {
            $host->setIsActive((bool) $data['isActive']);
        }

        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
