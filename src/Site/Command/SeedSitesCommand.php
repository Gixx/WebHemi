<?php

declare(strict_types=1);

namespace App\Site\Command;

use App\Entity\Site;
use App\Entity\SiteHost;
use App\Repository\SiteHostRepository;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:seed:sites', description: 'Seeds initial site and host records for local development.')]
final class SeedSitesCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SiteRepository $siteRepository,
        private readonly SiteHostRepository $siteHostRepository,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $site1 = $this->upsertSite('mysite', 'My Site');
        $this->upsertHost($site1, 'admin.mysite.local', 'admin');
        $this->upsertHost($site1, 'mysite.local', 'site');
        $this->upsertHost($site1, 'www.mysite.local', 'site');

        $site2 = $this->upsertSite('another', 'Another Site');
        $this->upsertHost($site2, 'another.mysite.local', 'site');

        $this->entityManager->flush();

        $io->success('Sites and hosts are seeded.');

        return Command::SUCCESS;
    }

    private function upsertSite(string $slug, string $name): Site
    {
        $normalizedSlug = strtolower(trim($slug));

        $site = $this->siteRepository->findOneBy(['slug' => $normalizedSlug]);
        if (!$site instanceof Site) {
            $site = new Site();
            $site->setSlug($normalizedSlug);
            $this->entityManager->persist($site);
        }

        $site->setName($name);
        $site->setIsEnabled(true);

        return $site;
    }

    private function upsertHost(Site $site, string $host, string $surface): void
    {
        $normalizedHost = strtolower(trim($host));

        $siteHost = $this->siteHostRepository->findOneBy(['host' => $normalizedHost]);
        if (!$siteHost instanceof SiteHost) {
            $siteHost = new SiteHost();
            $siteHost->setHost($normalizedHost);
            $this->entityManager->persist($siteHost);
        }

        $siteHost->setSite($site);
        $siteHost->setSurface($surface);
        $siteHost->setStatus('verified');
        $siteHost->setIsActive(true);
    }
}
