<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\TweetMedia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TweetMedia>
 *
 * @method TweetMedia|null find($id, $lockMode = null, $lockVersion = null)
 * @method TweetMedia|null findOneBy(array $criteria, array $orderBy = null)
 * @method TweetMedia[]    findAll()
 * @method TweetMedia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TweetMediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TweetMedia::class);
    }

    public function add(TweetMedia $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TweetMedia $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
