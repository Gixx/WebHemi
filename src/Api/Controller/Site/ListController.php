<?php

declare(strict_types=1);

namespace App\Api\Controller\Site;

use App\Entity\User;
use App\Repository\SiteAssignmentRepository;
use App\Repository\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/api/sites', name: 'admin_api_site_')]
#[IsGranted('site.list')]
class ListController extends AbstractController
{
    public function __construct(
        private readonly SiteRepository $siteRepository,
        private readonly SiteAssignmentRepository $siteAssignmentRepository,
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $sites = $this->siteRepository->findAll();
        } else {
            /** @var User $user */
            $user = $this->getUser();
            $sites = $this->siteAssignmentRepository->findSitesForUser($user);
        }

        return $this->json(array_map(static fn ($site) => [
            'id' => $site->getId(),
            'slug' => $site->getSlug(),
            'name' => $site->getName(),
            'isEnabled' => $site->isEnabled(),
        ], $sites));
    }
}
