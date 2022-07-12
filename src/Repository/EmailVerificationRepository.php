<?php

namespace App\Repository;

use App\Entity\EmailVerification;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmailVerification>
 *
 * @method EmailVerification|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailVerification|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailVerification[]    findAll()
 * @method EmailVerification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailVerificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailVerification::class);
    }

    public function add(EmailVerification $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EmailVerification $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param User $user
     * @return EmailVerification|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findLastForUser(User $user): ?EmailVerification
    {
        return $this->createQueryBuilder('v')
            ->where('v.author = :user')
            ->setParameter('user', $user)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Supprime les anciennes demande de verification d'email.
     */
    public function clean(): int
    {
        return $this->createQueryBuilder('v')
            ->where('v.createdAt < :date')
            ->setParameter('date', new DateTime('-1 month'))
            ->delete(EmailVerification::class, 'v')
            ->getQuery()
            ->execute();
    }
}
