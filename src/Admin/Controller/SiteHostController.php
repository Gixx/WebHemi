<?php

declare(strict_types=1);

namespace App\Admin\Controller;

use App\Entity\SiteHost;
use App\Repository\SiteHostRepository;
use App\Repository\SiteRepository;
use App\SiteHost\Verification\HostOwnershipVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/sites/{siteId}/hosts', name: 'admin_site_host_')]
final class SiteHostController extends AbstractController
{
    public function __construct(
        private readonly SiteRepository $siteRepository,
        private readonly SiteHostRepository $siteHostRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly HostOwnershipVerifier $hostOwnershipVerifier,
    ) {
    }

    #[Route(name: 'list', methods: ['GET'])]
    #[IsGranted('site.edit')]
    #[IsGranted('site.own', subject: 'siteId')]
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
    #[IsGranted('site.edit')]
    #[IsGranted('site.own', subject: 'siteId')]
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
            $submittedSurface = strtolower(trim($request->request->getString('surface', 'site')));

            if ('site' !== $submittedSurface) {
                $this->addFlash(
                    'failed',
                    'New hosts can only be created as public site hosts. '
                    . 'The admin surface is always available via the canonical /admin path.',
                );
            } else {
                $host->setSurface('site');
                $host->setIsActive($request->request->getBoolean('isActive', true));
            }

            if ('site' === $submittedSurface && $this->isValidHost($host)) {
                $verificationResult = $this->hostOwnershipVerifier->verify($host->getHost());
                $host->setStatus($verificationResult->verified ? 'verified' : 'pending');

                $this->entityManager->persist($host);
                $this->entityManager->flush();

                if ($verificationResult->verified) {
                    $this->addFlash('success', 'Host created and verified successfully.');
                } else {
                    $this->addFlash(
                        'warning',
                        'Host created in pending state. Use Verify after DNS/server setup is complete.',
                    );
                }

                return $this->redirectToRoute('admin_site_host_list', ['siteId' => $siteId]);
            }

            if ('site' === $submittedSurface) {
                $this->addFlash('failed', 'Please provide a valid hostname (for example: sub.example.com).');
            }
        }

        return $this->render('admin/site_host/form.html.twig', [
            'site' => $site,
            'host' => $host,
            'title' => 'Create Host',
        ]);
    }

    #[Route('/{hostId}/edit', name: 'edit', methods: ['GET', 'POST'])]
    #[IsGranted('site.edit')]
    #[IsGranted('site.own', subject: 'siteId')]
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
    #[IsGranted('site.edit')]
    #[IsGranted('site.own', subject: 'siteId')]
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

    #[Route('/{hostId}/verify', name: 'verify', methods: ['POST'])]
    #[IsGranted('site.edit')]
    #[IsGranted('site.own', subject: 'siteId')]
    public function verify(int $siteId, int $hostId): Response
    {
        $site = $this->siteRepository->find($siteId);
        if (null === $site) {
            throw $this->createNotFoundException('Site not found.');
        }

        $host = $this->siteHostRepository->find($hostId);
        if (null === $host || $host->getSite()->getId() !== $siteId) {
            throw $this->createNotFoundException('Host not found.');
        }

        $verificationResult = $this->hostOwnershipVerifier->verify($host->getHost());
        $host->setStatus($verificationResult->verified ? 'verified' : 'pending');
        $this->entityManager->flush();

        if ($verificationResult->verified) {
            $this->addFlash('success', 'Host verification succeeded.');
        } else {
            $this->addFlash('warning', 'Host verification failed. Keep status as pending and try again later.');
        }

        return $this->redirectToRoute('admin_site_host_list', ['siteId' => $siteId]);
    }

    private function isValidHost(SiteHost $host): bool
    {
        if ('' === $host->getHost() || 'site' !== $host->getSurface()) {
            return false;
        }

        return 1 === preg_match('/^(?!-)(?:[a-z0-9-]{1,63}\.)*[a-z0-9-]{1,63}$/', $host->getHost());
    }
}
