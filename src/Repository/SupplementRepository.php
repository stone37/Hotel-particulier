<?php

namespace App\Repository;

use App\Entity\Supplement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Supplement>
 *
 * @method Supplement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Supplement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Supplement[]    findAll()
 * @method Supplement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SupplementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Supplement::class);
    }

    public function add(Supplement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Supplement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getData(): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('s')
            ->orderBy('s.position', 'asc');

        return $qb;
    }
}
