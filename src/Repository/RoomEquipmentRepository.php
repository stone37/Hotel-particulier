<?php

namespace App\Repository;

use App\Entity\RoomEquipment;
use App\Model\EquipmentSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RoomEquipment>
 *
 * @method RoomEquipment|null find($id, $lockMode = null, $lockVersion = null)
 * @method RoomEquipment|null findOneBy(array $criteria, array $orderBy = null)
 * @method RoomEquipment[]    findAll()
 * @method RoomEquipment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoomEquipmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoomEquipment::class);
    }

    public function add(RoomEquipment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RoomEquipment $entity, bool $flush = false): void
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

    public function getAdmins(EquipmentSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('e')
            ->orderBy('e.position', 'asc');

        if ($search->getName())
            $qb->andWhere('e.name LIKE :name')->setParameter('name', '%'.$search->getName().'%');

        return $qb;
    }


    public function getData(): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('e')
            ->orderBy('e.position', 'asc');

        return $qb;
    }

}
