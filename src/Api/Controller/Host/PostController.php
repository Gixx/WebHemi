<?php

declare(strict_types=1);

namespace App\Api\Controller\Host;

use App\Entity\SiteHost;
use App\Entity\SurfaceType;
use App\Repository\SiteRepository;
use App\SiteHost\Verification\HostOwnershipVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/api/sites/{siteId<\d+>}/hosts', name: 'admin_api_site_host_')]
#[IsGranted('site.edit')]
#[IsGranted('site.own', subject: 'siteId')]
class PostController extends AbstractController
{
    public function __construct(
        private readonly SiteRepository $siteRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly HostOwnershipVerifier $hostOwnershipVerifier,
    ) {
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function __invoke(int $siteId, Request $request): JsonResponse
    {
        $site = $this->siteRepository->find($siteId);
        if (null === $site) {
            throw new NotFoundHttpException('Site not found.');
        }

        $data = $request->toArray();
        $hostname = strtolower(trim((string) ($data['host'] ?? '')));

        if (!$this->isValidHostname($hostname)) {
            throw new BadRequestHttpException('Please provide a valid hostname (for example: sub.example.com).');
        }

        $submittedSurface = SurfaceType::tryFrom(strtolower(trim((string) ($data['surface'] ?? ''))));
        if (SurfaceType::Site !== $submittedSurface) {
            throw new BadRequestHttpException(
                'New hosts can only be created as public site hosts. '
                . 'The admin surface is always available via the canonical /admin path.',
            );
        }

        $host = (new SiteHost())
            ->setSite($site)
            ->setHost($hostname)
            ->setSurface(SurfaceType::Site)
            ->setIsActive((bool) ($data['isActive'] ?? true));

        $verificationResult = $this->hostOwnershipVerifier->verify($hostname);
        $host->setStatus($verificationResult->verified ? 'verified' : 'pending');

        $this->entityManager->persist($host);
        $this->entityManager->flush();

        return $this->json([
            'id' => $host->getId(),
            'status' => $host->getStatus(),
        ], Response::HTTP_CREATED);
    }

    private function isValidHostname(string $hostname): bool
    {
        if ('' === $hostname) {
            return false;
        }

        return 1 === preg_match('/^(?!-)(?:[a-z0-9-]{1,63}\.)*[a-z0-9-]{1,63}$/', $hostname);
    }
}
