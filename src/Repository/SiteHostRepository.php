<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SiteHost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SiteHost>
 */
final class SiteHostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SiteHost::class);
    }

    /**
     * @return array{site_id:int, surface:string, host:string}|null
     */
    public function findContextByHost(string $normalizedHost): ?array
    {
        $row = $this->createQueryBuilder('siteHost')
            ->select('IDENTITY(siteHost.site) AS site_id', 'siteHost.surface AS surface', 'siteHost.host AS host')
            ->innerJoin('siteHost.site', 'site')
            ->andWhere('siteHost.host = :host')
            ->andWhere('siteHost.status IN (:statuses)')
            ->andWhere('siteHost.isActive = true')
            ->andWhere('site.isEnabled = true')
            ->setParameter('host', $normalizedHost)
            ->setParameter('statuses', ['verified', 'active'])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (null === $row) {
            return null;
        }

        return [
            'site_id' => (int) $row['site_id'],
            'surface' => (string) $row['surface'],
            'host' => (string) $row['host'],
        ];
    }

    public function findCanonicalSiteHost(int $siteId): ?string
    {
        $row = $this->createQueryBuilder('siteHost')
            ->select('siteHost.host')
            ->innerJoin('siteHost.site', 'site')
            ->andWhere('siteHost.site = :siteId')
            ->andWhere('siteHost.surface = :surface')
            ->andWhere('siteHost.status IN (:statuses)')
            ->andWhere('siteHost.isActive = true')
            ->andWhere('site.isEnabled = true')
            ->setParameter('siteId', $siteId)
            ->setParameter('surface', 'site')
            ->setParameter('statuses', ['verified', 'active'])
            ->orderBy('siteHost.host', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!is_array($row) || !isset($row['host']) || !is_string($row['host']) || '' === $row['host']) {
            return null;
        }

        return $row['host'];
    }
}
