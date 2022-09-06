<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Profile;
use App\Entity\ScheduledCommand;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ScheduledCommand>
 *
 * @method ScheduledCommand|null find($id, $lockMode = null, $lockVersion = null)
 * @method ScheduledCommand|null findOneBy(array $criteria, array $orderBy = null)
 * @method ScheduledCommand[]    findAll()
 * @method ScheduledCommand[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScheduledCommandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScheduledCommand::class);
    }

    public function add(ScheduledCommand $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ScheduledCommand $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /** @return ScheduledCommand[] */
    public function getPendingScheduledCommands(Profile $profile, int $limit): array
    {
        $builder = $this->createQueryBuilder('c');

        return $builder
            ->select('c')
            ->where(
                $builder->expr()->lte('c.due', ':now')
            )
            ->andWhere($builder->expr()->eq('c.profile', ':profile'))
            ->setMaxResults($limit)
            ->setParameter('now', new DateTimeImmutable('now'), 'datetime_immutable')
            ->setParameter('profile', $profile)
            ->getQuery()
            ->getResult();
    }

    public function getAllPendingScheduledCommands(int $limit): array
    {
        $builder = $this->createQueryBuilder('c');

        return $builder
            ->select('c')
            ->where(
                $builder->expr()->lte('c.due', ':now')
            )
            ->setMaxResults($limit)
            ->setParameter('now', new DateTimeImmutable('now'), 'datetime_immutable')
            ->getQuery()
            ->getResult();
    }
}
