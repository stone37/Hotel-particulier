<?php

namespace App\Repository;

use App\Entity\Room;
use App\Model\RoomFilter;
use App\Model\RoomSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Room>
 *
 * @method Room|null find($id, $lockMode = null, $lockVersion = null)
 * @method Room|null findOneBy(array $criteria, array $orderBy = null)
 * @method Room[]    findAll()
 * @method Room[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }

    public function add(Room $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Room $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Retourne tous les chambres dans l'adminnistration
     *
     * @param RoomSearch $search
     * @return QueryBuilder|null
     */
    public function getAdmins(RoomSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.bookings', 'bookings')
            ->leftJoin('r.equipments', 'equipments')
            ->leftJoin('r.galleries', 'galleries')
            ->leftJoin('r.supplements', 'supplements')
            ->leftJoin('r.promotions', 'promotions')
            ->leftJoin('r.options', 'options')
            ->leftJoin('r.taxe', 'taxe')
            ->leftJoin('options.supplements', 'opt_supplements')
            ->addSelect('bookings')
            ->addSelect('equipments')
            ->addSelect('galleries')
            ->addSelect('supplements')
            ->addSelect('promotions')
            ->addSelect('options')
            ->addSelect('opt_supplements')
            ->addSelect('taxe')
            ->orderBy('r.position', 'asc');

        if ($search->isEnabled())
            $qb->andWhere('r.enabled = 1');

        if ($search->getName())
            $qb->andWhere('r.name LIKE :name')->setParameter('name', '%'.$search->getName().'%');

        return $qb;
    }

    /**
     * Retourne tous les chambre active
     *
     * @return int|mixed|string
     */
    public function getEnabled()
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.bookings', 'bookings')
            ->leftJoin('r.equipments', 'equipments')
            ->leftJoin('r.galleries', 'galleries')
            ->leftJoin('r.supplements', 'supplements')
            ->leftJoin('r.promotions', 'promotions')
            ->leftJoin('r.options', 'options')
            ->leftJoin('r.taxe', 'taxe')
            ->leftJoin('options.supplements', 'opt_supplements')
            ->addSelect('bookings')
            ->addSelect('equipments')
            ->addSelect('galleries')
            ->addSelect('supplements')
            ->addSelect('promotions')
            ->addSelect('options')
            ->addSelect('opt_supplements')
            ->addSelect('taxe')
            ->andWhere('r.enabled = 1')
            ->orderBy('r.position', 'asc');

        return $qb->getQuery()->getResult();
    }

    public function getFilter(RoomFilter $filter)
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.bookings', 'bookings')
            ->leftJoin('r.equipments', 'equipments')
            ->leftJoin('r.galleries', 'galleries')
            ->leftJoin('r.supplements', 'supplements')
            ->leftJoin('r.promotions', 'promotions')
            ->leftJoin('r.options', 'options')
            ->leftJoin('r.taxe', 'taxe')
            ->leftJoin('options.supplements', 'opt_supplements')
            ->addSelect('bookings')
            ->addSelect('equipments')
            ->addSelect('galleries')
            ->addSelect('supplements')
            ->addSelect('promotions')
            ->addSelect('options')
            ->addSelect('opt_supplements')
            ->addSelect('taxe')
            ->andWhere('r.enabled = 1')
            ->orderBy('r.position', 'asc');

        if ($filter->getAdult()) {
            $qb->andWhere('r.maximumAdults >= :adults')
                ->setParameter('adults', $filter->getAdult());
        }

        if ($filter->getChildren()) {
            $qb->andWhere('r.maximumOfChildren >= :children')
                ->setParameter('children', $filter->getChildren());
        }

        return $qb->getQuery()->getResult();
    }

    public function findRandom(int $limit): array
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.bookings', 'bookings')
            ->leftJoin('r.equipments', 'equipments')
            ->leftJoin('r.galleries', 'galleries')
            ->leftJoin('r.supplements', 'supplements')
            ->leftJoin('r.promotions', 'promotions')
            ->leftJoin('r.options', 'options')
            ->leftJoin('r.taxe', 'taxe')
            ->leftJoin('options.supplements', 'opt_supplements')
            ->addSelect('bookings')
            ->addSelect('equipments')
            ->addSelect('galleries')
            ->addSelect('supplements')
            ->addSelect('promotions')
            ->addSelect('options')
            ->addSelect('opt_supplements')
            ->addSelect('taxe')
            ->where('r.enabled = 1')
            ->orderBy('r.position', 'asc')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getBySlug(string $slug)
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.bookings', 'bookings')
            ->leftJoin('r.equipments', 'equipments')
            ->leftJoin('r.galleries', 'galleries')
            ->leftJoin('r.supplements', 'supplements')
            ->leftJoin('r.promotions', 'promotions')
            ->leftJoin('r.options', 'options')
            ->leftJoin('r.taxe', 'taxe')
            ->leftJoin('options.supplements', 'opt_supplements')
            ->addSelect('bookings')
            ->addSelect('equipments')
            ->addSelect('galleries')
            ->addSelect('supplements')
            ->addSelect('promotions')
            ->addSelect('options')
            ->addSelect('opt_supplements')
            ->addSelect('taxe')
            ->where('r.slug = :slug')
            //->andWhere('r.enabled = 1')
            ->setParameter('slug', $slug);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getMaximumAdult()
    {
        $qb = $this->createQueryBuilder('r')
            ->select('r.maximumAdults')
            ->orderBy('r.maximumAdults', 'desc')
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getMaximumChildren()
    {
        $qb = $this->createQueryBuilder('r')
            ->select('r.maximumOfChildren')
            ->orderBy('r.maximumOfChildren', 'desc')
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function roomListeQueryBuilder()
    {
        $qb = $this->createQueryBuilder('r')
            ->orderBy('r.position', 'asc');

        return $qb;
    }

    public function getRoomTotalNumber(): ?int
    {
        $qb = $this->createQueryBuilder('r')
            ->select('SUM(r.roomNumber)');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function getRoomEnabledTotalNumber(): ?int
    {
        $qb = $this->createQueryBuilder('r')
            ->select('SUM(r.roomNumber)')
            ->where('r.enabled = 1');

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getWithFilters()
    {
        $results = $this->createQueryBuilder('r')
            ->andWhere('r.enabled = 1')
            ->orderBy('r.position', 'asc')
            ->getQuery()->getArrayResult();

        $data = [];

        foreach ($results as $result) {
            $data[$result['name']] = $result['id'];
        }

        return $data;
    }

}
