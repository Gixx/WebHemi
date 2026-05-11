<?php

declare(strict_types=1);

namespace App\Api\Controller\Site;

use App\Repository\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/api/sites/{id<\d+>}', name: 'admin_api_site_')]
#[IsGranted('site.view', subject: 'id')]
#[IsGranted('site.own', subject: 'id')]
class GetController extends AbstractController
{
    public function __construct(private readonly SiteRepository $siteRepository)
    {
    }

    #[Route('', name: 'view', methods: ['GET'])]
    public function __invoke(int $id): JsonResponse
    {
        $site = $this->siteRepository->find($id);
        if (null === $site) {
            throw new NotFoundHttpException('Site not found.');
        }

        return $this->json([
            'id' => $site->getId(),
            'slug' => $site->getSlug(),
            'name' => $site->getName(),
            'isEnabled' => $site->isEnabled(),
        ]);
    }
}
