<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\TwitterProfileStat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TwitterProfileStat>
 *
 * @method TwitterProfileStat|null find($id, $lockMode = null, $lockVersion = null)
 * @method TwitterProfileStat|null findOneBy(array $criteria, array $orderBy = null)
 * @method TwitterProfileStat[]    findAll()
 * @method TwitterProfileStat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TwitterProfileStatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TwitterProfileStat::class);
    }

    public function add(TwitterProfileStat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TwitterProfileStat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
