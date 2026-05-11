<?php

declare(strict_types=1);

namespace App\Api\Controller\Host;

use App\Repository\SiteHostRepository;
use App\Repository\SiteRepository;
use App\SiteHost\Verification\HostOwnershipVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/api/sites/{siteId<\d+>}/hosts/{hostId<\d+>}/verify', name: 'admin_api_site_host_')]
#[IsGranted('site.edit')]
#[IsGranted('site.own', subject: 'siteId')]
class VerifyController extends AbstractController
{
    public function __construct(
        private readonly SiteRepository $siteRepository,
        private readonly SiteHostRepository $siteHostRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly HostOwnershipVerifier $hostOwnershipVerifier,
    ) {
    }

    #[Route('', name: 'verify', methods: ['POST'])]
    public function __invoke(int $siteId, int $hostId): JsonResponse
    {
        $site = $this->siteRepository->find($siteId);
        if (null === $site) {
            throw new NotFoundHttpException('Site not found.');
        }

        $host = $this->siteHostRepository->find($hostId);
        if (null === $host || $host->getSite()->getId() !== $siteId) {
            throw new NotFoundHttpException('Host not found.');
        }

        $verificationResult = $this->hostOwnershipVerifier->verify($host->getHost());
        $host->setStatus($verificationResult->verified ? 'verified' : 'pending');
        $this->entityManager->flush();

        return $this->json([
            'verified' => $verificationResult->verified,
            'status' => $host->getStatus(),
        ]);
    }
}
