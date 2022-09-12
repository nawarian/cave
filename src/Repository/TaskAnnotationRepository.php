<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\TaskAnnotation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TaskAnnotation>
 *
 * @method TaskAnnotation|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskAnnotation|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskAnnotation[]    findAll()
 * @method TaskAnnotation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskAnnotationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskAnnotation::class);
    }

    public function add(TaskAnnotation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TaskAnnotation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
