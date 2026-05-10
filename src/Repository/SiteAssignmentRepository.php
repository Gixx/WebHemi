<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Site;
use App\Entity\SiteAssignment;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<SiteAssignment> */
class SiteAssignmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SiteAssignment::class);
    }

    public function findForUserAndSite(User $user, int $siteId): ?SiteAssignment
    {
        return $this->createQueryBuilder('sa')
            ->where('sa.user = :user')
            ->andWhere('IDENTITY(sa.site) = :siteId')
            ->setParameter('user', $user)
            ->setParameter('siteId', $siteId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Site[]
     */
    public function findSitesForUser(User $user): array
    {
        /** @var SiteAssignment[] $assignments */
        $assignments = $this->findBy(['user' => $user]);

        return array_map(static fn (SiteAssignment $sa) => $sa->getSite(), $assignments);
    }
}
