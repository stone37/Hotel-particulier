<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\Room;
use App\Entity\User;
use App\Model\BookingSearch;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 *
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    public function add(Booking $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Booking $entity, bool $flush = false): void
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

    public function getAdmins(BookingSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('b');

        $qb->leftJoin('b.user', 'user')
            ->leftJoin('b.room', 'room')
            ->leftJoin('b.commande', 'commande')
            ->leftJoin('b.occupants', 'occupants')
            ->addSelect('user')
            ->addSelect('room')
            ->addSelect('commande')
            ->addSelect('occupants')
            ->where($qb->expr()->isNull('b.confirmedAt'))
            ->andWhere('b.checkin >= :date')
            ->setParameter('date', new DateTime())
            ->orderBy('b.createdAt', 'desc');

        if ($search->getCode())
            $qb->andWhere('b.reference = :reference')->setParameter('reference', $search->getCode());

        if ($search->getUser())
            $qb->andWhere('b.user = :user')->setParameter('user', (int)$search->getUser());

        if ($search->getRoom())
            $qb->andWhere('b.room = :room')->setParameter('room', (int)$search->getRoom());

        return $qb;
    }

    public function getConfirmAdmins(BookingSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('b');
        $qb->leftJoin('b.user', 'user')
            ->leftJoin('b.room', 'room')
            ->addSelect('user')
            ->addSelect('room')
            ->where($qb->expr()->isNotNull('b.confirmedAt'))
            ->andWhere('b.checkout > :date')
            ->setParameter('date', new DateTime())
            ->orderBy('b.createdAt', 'desc');

        if ($search->getCode())
            $qb->andWhere('b.reference = :reference')->setParameter('reference', $search->getCode());

        if ($search->getUser())
            $qb->andWhere('b.user = :user')->setParameter('user', (int)$search->getUser());

        if ($search->getRoom())
            $qb->andWhere('b.room = :room')->setParameter('room', (int)$search->getRoom());

        return $qb;
    }

    public function getCancelAdmins(BookingSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('b');

        $qb->leftJoin('b.user', 'user')
            ->leftJoin('b.room', 'room')
            ->addSelect('user')
            ->addSelect('room')
            ->where('b.status = :status')
            ->setParameter('status', Booking::CANCELLED)
            ->orderBy('b.createdAt', 'desc');

        if ($search->getCode())
            $qb->andWhere('b.reference = :reference')->setParameter('reference', $search->getCode());

        if ($search->getUser())
            $qb->andWhere('b.user = :user')->setParameter('user', (int)$search->getUser());

        if ($search->getRoom())
            $qb->andWhere('b.room = :room')->setParameter('room', (int)$search->getRoom());

        return $qb;
    }

    public function getCancel()
    {
        $qb = $this->createQueryBuilder('b')
            ->orderBy('b.createdAt', 'desc');

        $qb->andWhere($qb->expr()->isNull('b.cancelledAt'))
            ->andWhere('b.checkin <= :date')
            ->setParameter('date', new DateTime());

        return $qb->getQuery()->getResult();
    }

    public function getArchiveAdmins(BookingSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('b');
        $qb->leftJoin('b.user', 'user')
            ->leftJoin('b.room', 'room')
            ->addSelect('user')
            ->addSelect('room')
            ->where($qb->expr()->isNotNull('b.confirmedAt'))
            ->andWhere('b.checkout < :date')
            ->setParameter('date', new DateTime())
            ->orderBy('b.createdAt', 'desc');

        if ($search->getCode())
            $qb->andWhere('b.reference = :reference')->setParameter('reference', $search->getCode());

        if ($search->getUser())
            $qb->andWhere('b.user = :user')->setParameter('user', (int)$search->getUser());

        if ($search->getRoom())
            $qb->andWhere('b.room = :room')->setParameter('room', (int)$search->getRoom());

        return $qb;
    }

    public function admins(BookingSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('b');
        $qb->leftJoin('b.user', 'user')
            ->leftJoin('b.room', 'room')
            ->addSelect('user')
            ->addSelect('room')
            ->orderBy('b.createdAt', 'desc');

        if ($search->getCode())
            $qb->andWhere('b.reference = :reference')->setParameter('reference', $search->getCode());

        if ($search->getUser())
            $qb->andWhere('b.user = :user')->setParameter('user', (int)$search->getUser());

        if ($search->getRoom())
            $qb->andWhere('b.room = :room')->setParameter('room', (int)$search->getRoom());

        return $qb;
    }

    public function getByUser(BookingSearch $search, User $user, $state = null)
    {
        $qb = $this->createQueryBuilder('b')
            ->leftJoin('b.user', 'user')
            ->leftJoin('b.room', 'room')
            ->leftJoin('b.commande', 'commande')
            ->leftJoin('commande.payment', 'payment')
            ->addSelect('user')
            ->addSelect('room')
            ->addSelect('commande')
            ->addSelect('payment')
            ->where('b.user = :user')
            ->setParameter('user', $user)
            ->orderBy('b.createdAt', 'desc');

        if ($state === Booking::NEW) {
            $qb->andWhere($qb->expr()->isNull('b.confirmedAt'))
                ->andWhere('b.checkin >= :date')
                ->andWhere('b.status = :status')
                ->setParameter('date', new DateTime())
                ->setParameter('status', Booking::NEW);
        } elseif ($state === Booking::CONFIRMED) {
            $qb->andWhere($qb->expr()->isNotNull('b.confirmedAt'))
                ->andWhere('b.status = :status')
                ->setParameter('status', Booking::CONFIRMED);
        }  elseif ($state === Booking::CANCELLED) {
            $qb->andWhere($qb->expr()->isNotNull('b.cancelledAt'))
                ->andWhere('b.status = :status')
                ->setParameter('status', Booking::CANCELLED);
        } else {
            $qb->andWhere($qb->expr()->isNull('b.cancelledAt'))
                ->andWhere('b.checkin <= :date')
                ->setParameter('date', new DateTime());
        }

        if ($search->getCode())
            $qb->andWhere('b.reference = :reference')->setParameter('reference', $search->getCode());

        return $qb->getQuery()->getResult();
    }

    public function countUserEnabled(User $user)
    {
        $qb = $this->createQueryBuilder('b')
            ->select('count(b.id)');
        $qb->where('b.user = :user')
            ->setParameter('user', $user)
            ->andWhere($qb->expr()->isNotNull('b.confirmedAt'));

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function deleteForUser(User $user): void
    {
        $this->createQueryBuilder('b')
            ->where('b.user = :user')
            ->setParameter('user', $user)
            ->delete()
            ->getQuery()
            ->execute();
    }

    public function availableForPeriod(Room $room, DateTime $start, DateTime $end)
    {
        $qb = $this->createQueryBuilder('b');
        $query = $qb->select('SUM(b.roomNumber)')
            ->where('b.checkin <= :start AND b.checkout >= :end')
            ->orWhere('b.checkin >= :start AND b.checkout <= :end')
            ->orWhere('b.checkin >= :start AND b.checkout >= :end AND b.checkin <= :end')
            ->orWhere('b.checkin <= :start AND b.checkout <= :end AND b.checkout >= :start')
            ->andWhere('b.room = :room')
            ->setParameters(['start'=> $start, 'end'  => $end, 'room' => $room]);

        return (int) $query->getQuery()->getSingleScalarResult();
    }

    public function getRoomBookingTotalNumber(DateTime $start, DateTime $end): ?int
    {
        $qb = $this->createQueryBuilder('b');
        $query = $qb->select('SUM(b.roomNumber)')
            ->where('b.checkin <= :start AND b.checkout >= :end')
            ->orWhere('b.checkin >= :start AND b.checkout <= :end')
            ->orWhere('b.checkin >= :start AND b.checkout >= :end AND b.checkin <= :end')
            ->orWhere('b.checkin <= :start AND b.checkout <= :end AND b.checkout >= :start')
            ->setParameters(['start'=> $start, 'end'  => $end]);

        return (int) $query->getQuery()->getSingleScalarResult();
    }

    public function getLasts()
    {
        $qb = $this->createQueryBuilder('b')
            ->leftJoin('b.user', 'user')
            ->leftJoin('b.room', 'room')
            ->addSelect('user')
            ->addSelect('room')
            ->orderBy('b.createdAt', 'desc')
            ->setMaxResults(10);

        return $qb->getQuery()->getResult();
    }

    public function getNewNumber(): ?int
    {
        $qb = $this->createQueryBuilder('b')
            ->select('count(b.id)');

        $qb->where($qb->expr()->isNull('b.confirmedAt'))
            ->andWhere('b.checkout >= :date')
            ->setParameter('date', new DateTime());

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getConfirmNumber(): ?int
    {
        $qb = $this->createQueryBuilder('b')
            ->select('count(b.id)');

        $qb->where($qb->expr()->isNotNull('b.confirmedAt'))
            ->andWhere('b.checkout > :date')
            ->setParameter('date', new DateTime());


        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getCancelNumber(): ?int
    {
        $qb = $this->createQueryBuilder('b')
            ->select('count(b.id)');

        $qb->where('b.status = :status')
            ->setParameter('status', Booking::CANCELLED);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getArchiveNumber(): ?int
    {
        $qb = $this->createQueryBuilder('b')
            ->select('count(b.id)');

        $qb->where($qb->expr()->isNotNull('b.confirmedAt'))
            ->andWhere('b.checkout < :date')
            ->setParameter('date', new DateTime());

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getArchiveDays()
    {
        $qb = $this->createQueryBuilder('b')
            ->select('SUM(b.days) as days');

        $qb->where($qb->expr()->isNotNull('b.confirmedAt'))
            ->andWhere('b.checkout < :date')
            ->setParameter('date', new DateTime());
    }

    public function getConfirmedByUserNumber(User $user)
    {
        $qb = $this->createQueryBuilder('b')
            ->select('count(b.id)');

        $qb->where($qb->expr()->isNotNull('b.confirmedAt'))
            ->andWhere('b.user = :user')
            ->andWhere('b.checkout > :date')
            ->setParameter('user', $user)
            ->setParameter('date', new DateTime());


        return $qb->getQuery()->getSingleScalarResult();
    }
}
