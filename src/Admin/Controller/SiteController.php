<?php

declare(strict_types=1);

namespace App\Admin\Controller;

use App\Entity\Site;
use App\Entity\User;
use App\Repository\SiteAssignmentRepository;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/sites', name: 'admin_site_')]
final class SiteController extends AbstractController
{
    public function __construct(
        private readonly SiteRepository $siteRepository,
        private readonly SiteAssignmentRepository $siteAssignmentRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route(name: 'list', methods: ['GET'])]
    #[IsGranted('site.list')]
    public function list(): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $sites = $this->siteRepository->findAll();
        } else {
            /** @var User $user */
            $user = $this->getUser();
            $sites = $this->siteAssignmentRepository->findSitesForUser($user);
        }

        return $this->render('admin/site/list.html.twig', [
            'sites' => $sites,
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request): Response
    {
        $site = new Site();

        if ($request->isMethod(Request::METHOD_POST)) {
            $site->setSlug($request->request->getString('slug', ''));
            $site->setName($request->request->getString('name', ''));
            $site->setIsEnabled($request->request->getBoolean('isEnabled', true));

            if ($this->isValidSite($site)) {
                $this->entityManager->persist($site);
                $this->entityManager->flush();

                $this->addFlash('success', 'Site created successfully.');

                return $this->redirectToRoute('admin_site_list');
            }

            $this->addFlash('failed', 'Slug and name are required.');
        }

        return $this->render('admin/site/form.html.twig', [
            'site' => $site,
            'title' => 'Create Site',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'show', methods: ['GET'])]
    #[IsGranted('site.view')]
    #[IsGranted('site.own', subject: 'id')]
    public function show(int $id): Response
    {
        $site = $this->siteRepository->find($id);
        if (null === $site) {
            throw $this->createNotFoundException('Site not found.');
        }

        return $this->render('admin/site/show.html.twig', [
            'site' => $site,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'edit', methods: ['GET', 'POST'])]
    #[IsGranted('site.edit')]
    #[IsGranted('site.own', subject: 'id')]
    public function edit(int $id, Request $request): Response
    {
        $site = $this->siteRepository->find($id);
        if (null === $site) {
            throw $this->createNotFoundException('Site not found.');
        }

        if ($request->isMethod(Request::METHOD_POST)) {
            $site->setName($request->request->getString('name', ''));
            $site->setIsEnabled($request->request->getBoolean('isEnabled', true));

            $this->entityManager->flush();
            $this->addFlash('success', 'Site updated successfully.');

            return $this->redirectToRoute('admin_site_list');
        }

        return $this->render('admin/site/form.html.twig', [
            'site' => $site,
            'title' => 'Edit Site',
        ]);
    }

    #[Route('/{id<\d+>}/delete', name: 'delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id): Response
    {
        $site = $this->siteRepository->find($id);
        if (null === $site) {
            throw $this->createNotFoundException('Site not found.');
        }

        $this->entityManager->remove($site);
        $this->entityManager->flush();
        $this->addFlash('success', 'Site deleted successfully.');

        return $this->redirectToRoute('admin_site_list');
    }

    private function isValidSite(Site $site): bool
    {
        return '' !== $site->getSlug() && '' !== $site->getName();
    }
}
