<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\GoogleStat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GoogleStat>
 *
 * @method GoogleStat|null find($id, $lockMode = null, $lockVersion = null)
 * @method GoogleStat|null findOneBy(array $criteria, array $orderBy = null)
 * @method GoogleStat[]    findAll()
 * @method GoogleStat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GoogleStatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GoogleStat::class);
    }

    public function add(GoogleStat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(GoogleStat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}

