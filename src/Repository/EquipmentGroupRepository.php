<?php

namespace App\Repository;

use App\Entity\EquipmentGroup;
use App\Model\EquipmentGroupSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EquipmentGroup>
 *
 * @method EquipmentGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method EquipmentGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method EquipmentGroup[]    findAll()
 * @method EquipmentGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquipmentGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EquipmentGroup::class);
    }

    public function add(EquipmentGroup $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EquipmentGroup $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function getAdmins(EquipmentGroupSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('eg')
            ->orderBy('eg.name', 'asc');

        if ($search->getName())
            $qb->andWhere('eg.name LIKE :name')->setParameter('name', '%'.$search->getName().'%');

        return $qb;
    }

    public function getEnabled(): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('eg')
            ->orderBy('eg.name', 'asc');

        return $qb;
    }

    public function getAll()
    {
        $qb = $this->createQueryBuilder('eg')
            ->leftJoin('eg.equipments', 'equipments')
            ->addSelect('equipments')
            ->orderBy('eg.name', 'asc');

        return $qb->getQuery()->getResult();
    }
}
