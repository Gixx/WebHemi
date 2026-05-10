<?php

declare(strict_types=1);

namespace App\Admin\Controller;

use App\Entity\User;
use App\Repository\SiteAssignmentRepository;
use App\Repository\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'admin_')]
final class DashboardController extends AbstractController
{
    public function __construct(
        private readonly SiteRepository $siteRepository,
        private readonly SiteAssignmentRepository $siteAssignmentRepository,
    ) {
    }

    #[Route('', name: 'dashboard', methods: ['GET'])]
    #[IsGranted('dashboard.view')]
    public function dashboard(): Response
    {
        $sites = [];
        if ($this->isGranted('site.edit')) {
            if ($this->isGranted('ROLE_ADMIN')) {
                $sites = $this->siteRepository->findBy([], ['name' => 'ASC']);
            } else {
                /** @var User $user */
                $user = $this->getUser();
                $sites = $this->siteAssignmentRepository->findSitesForUser($user);
            }
        }

        return $this->render('admin/dashboard.html.twig', [
            'sitesForDesktop' => $sites,
        ]);
    }
}
