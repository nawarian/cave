<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\TwitterThread;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TwitterThread>
 *
 * @method TwitterThread|null find($id, $lockMode = null, $lockVersion = null)
 * @method TwitterThread|null findOneBy(array $criteria, array $orderBy = null)
 * @method TwitterThread[]    findAll()
 * @method TwitterThread[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TwitterThreadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TwitterThread::class);
    }

    public function add(TwitterThread $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TwitterThread $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
