<?php

namespace App\Repository;

use App\Entity\RoomGallery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RoomGallery>
 *
 * @method RoomGallery|null find($id, $lockMode = null, $lockVersion = null)
 * @method RoomGallery|null findOneBy(array $criteria, array $orderBy = null)
 * @method RoomGallery[]    findAll()
 * @method RoomGallery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoomGalleryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoomGallery::class);
    }

    public function add(RoomGallery $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RoomGallery $entity, bool $flush = false): void
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

    public function getGalleries(int $limit = 3)
    {
        $qb = $this->createQueryBuilder('rg')->select('COUNT(rg)');

        $totalRecords = $qb->getQuery()->getSingleScalarResult();

        if ($totalRecords < 1) {
            return null;
        }

        if ($totalRecords < $limit) {
            return $qb->select('rg')
                ->setMaxResults($limit)
                ->getQuery()
                ->getResult();
        } else {
            return $qb->select('rg')
                ->setMaxResults($limit)
                ->setFirstResult(rand(0, $totalRecords - $limit))
                ->getQuery()
                ->getResult();
        }
    }
}
