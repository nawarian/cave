<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\TwitterProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TwitterProfile>
 *
 * @method TwitterProfile|null find($id, $lockMode = null, $lockVersion = null)
 * @method TwitterProfile|null findOneBy(array $criteria, array $orderBy = null)
 * @method TwitterProfile[]    findAll()
 * @method TwitterProfile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TwitterProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TwitterProfile::class);
    }

    public function add(TwitterProfile $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TwitterProfile $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
