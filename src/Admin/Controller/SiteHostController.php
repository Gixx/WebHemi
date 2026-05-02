<?php

declare(strict_types=1);

namespace App\Admin\Controller;

use App\Entity\SiteHost;
use App\Repository\SiteHostRepository;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/sites/{siteId}/hosts', name: 'admin_site_host_')]
final class SiteHostController extends AbstractController
{
    public function __construct(
        private readonly SiteRepository $siteRepository,
        private readonly SiteHostRepository $siteHostRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route(name: 'list', methods: ['GET'])]
    public function list(int $siteId): Response
    {
        $site = $this->siteRepository->find($siteId);
        if (null === $site) {
            throw $this->createNotFoundException('Site not found.');
        }

        $hosts = $this->siteHostRepository->findBy(['site' => $site]);

        return $this->render('admin/site_host/list.html.twig', [
            'site' => $site,
            'hosts' => $hosts,
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(int $siteId, Request $request): Response
    {
        $site = $this->siteRepository->find($siteId);
        if (null === $site) {
            throw $this->createNotFoundException('Site not found.');
        }

        $host = new SiteHost();
        $host->setSite($site);

        if ($request->isMethod(Request::METHOD_POST)) {
            $host->setHost($request->request->getString('host', ''));
            $host->setSurface($request->request->getString('surface', 'site'));
            $host->setStatus('pending');
            $host->setIsActive($request->request->getBoolean('isActive', true));

            if ($this->isValidHost($host)) {
                $this->entityManager->persist($host);
                $this->entityManager->flush();

                $this->addFlash('success', 'Host created successfully.');

                return $this->redirectToRoute('admin_site_host_list', ['siteId' => $siteId]);
            }
        }

        return $this->render('admin/site_host/form.html.twig', [
            'site' => $site,
            'host' => $host,
            'title' => 'Create Host',
        ]);
    }

    #[Route('/{hostId}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(int $siteId, int $hostId, Request $request): Response
    {
        $site = $this->siteRepository->find($siteId);
        if (null === $site) {
            throw $this->createNotFoundException('Site not found.');
        }

        $host = $this->siteHostRepository->find($hostId);
        if (null === $host || $host->getSite()->getId() !== $siteId) {
            throw $this->createNotFoundException('Host not found.');
        }

        if ($request->isMethod(Request::METHOD_POST)) {
            $host->setStatus($request->request->getString('status', 'pending'));
            $host->setIsActive($request->request->getBoolean('isActive', true));

            $this->entityManager->flush();
            $this->addFlash('success', 'Host updated successfully.');

            return $this->redirectToRoute('admin_site_host_list', ['siteId' => $siteId]);
        }

        return $this->render('admin/site_host/form.html.twig', [
            'site' => $site,
            'host' => $host,
            'title' => 'Edit Host',
        ]);
    }

    #[Route('/{hostId}/delete', name: 'delete', methods: ['POST'])]
    public function delete(int $siteId, int $hostId): Response
    {
        $site = $this->siteRepository->find($siteId);
        if (null === $site) {
            throw $this->createNotFoundException('Site not found.');
        }

        $host = $this->siteHostRepository->find($hostId);
        if (null === $host || $host->getSite()->getId() !== $siteId) {
            throw $this->createNotFoundException('Host not found.');
        }

        $this->entityManager->remove($host);
        $this->entityManager->flush();
        $this->addFlash('success', 'Host deleted successfully.');

        return $this->redirectToRoute('admin_site_host_list', ['siteId' => $siteId]);
    }

    private function isValidHost(SiteHost $host): bool
    {
        return '' !== $host->getHost() && '' !== $host->getSurface();
    }
}
