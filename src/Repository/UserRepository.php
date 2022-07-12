<?php

namespace App\Repository;

use App\Entity\User;
use App\Model\UserSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function add(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->add($user, true);
    }

    /**
     * @param UserSearch $search
     * @return QueryBuilder|null
     */
    public function getAdmins(UserSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('u');

        $qb->where($qb->expr()->isNull('u.deleteAt'))
            ->andWhere('u.roles LIKE :roles')
            ->setParameter('roles', '%'."ROLE_ADMIN".'%')
            ->orderBy('u.createdAt', 'desc');

        if ($search->getEmail())
            $qb->andWhere('u.email LIKE :email')->setParameter('email', '%'.$search->getEmail().'%');

        if ($search->getPhone())
            $qb->andWhere('u.phone LIKE :phone')->setParameter('phone', '%'.$search->getPhone().'%');

        if ($search->isEnabled())
            $qb->andWhere('u.deleteAt IS NULL')->andWhere('u.confirmationToken IS NULL');

        return $qb;
    }

    /**
     * @param UserSearch $search
     * @return QueryBuilder|null
     */
    public function getAdminUsers(UserSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.deleteAt IS NULL')
            ->andWhere('u.confirmationToken IS NULL')
            ->andWhere('u.roles LIKE :roles')
            ->andWhere('u.roles NOT LIKE :roleP')
            ->andWhere('u.roles NOT LIKE :roleA')
            ->andWhere('u.roles NOT LIKE :roleSA')
            ->setParameter('roles', '%'."".'%')
            ->setParameter('roleP', '%'."ROLE_PARTNER".'%')
            ->setParameter('roleA', '%'."ROLE_ADMIN".'%')
            ->setParameter('roleSA', '%'."ROLE_SUPER_ADMIN".'%')
            ->orderBy('u.createdAt', 'desc');

        if ($search->getEmail())
            $qb->andWhere('u.email LIKE :email')->setParameter('email', '%'.$search->getEmail().'%');

        if ($search->getPhone())
            $qb->andWhere('u.phone LIKE :phone')->setParameter('phone', '%'.$search->getPhone().'%');

        if ($search->getCity())
            $qb->andWhere('location.name = :city')->setParameter('city', $search->getCity());

        return $qb;
    }

    /**
     * @param UserSearch $search
     * @return QueryBuilder|null
     */
    public function getUserNoConfirmed(UserSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.deleteAt IS NULL')
            ->andWhere('u.confirmationToken IS NOT NULL')
            ->andWhere('u.roles LIKE :roles')
            ->andWhere('u.roles NOT LIKE :roleP')
            ->andWhere('u.roles NOT LIKE :roleA')
            ->andWhere('u.roles NOT LIKE :roleSA')
            ->setParameter('roles', '%'."".'%')
            ->setParameter('roleP', '%'."ROLE_PARTNER".'%')
            ->setParameter('roleA', '%'."ROLE_ADMIN".'%')
            ->setParameter('roleSA', '%'."ROLE_SUPER_ADMIN".'%')
            ->orderBy('u.createdAt', 'desc');

        if ($search->getEmail())
            $qb->andWhere('u.email LIKE :email')->setParameter('email', '%'.$search->getEmail().'%');

        if ($search->getPhone())
            $qb->andWhere('u.phone LIKE :phone')->setParameter('phone', '%'.$search->getPhone().'%');

        if ($search->getCity())
            $qb->andWhere('location.name = :city')->setParameter('city', $search->getCity());

        return $qb;
    }

    /**
     * @param UserSearch $search
     * @return QueryBuilder|null
     */
    public function getUserDeleted(UserSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.deleteAt IS NOT NULL')
            ->andWhere('u.confirmationToken IS NULL')
            ->andWhere('u.roles LIKE :roles')
            ->andWhere('u.roles NOT LIKE :roleA')
            ->andWhere('u.roles NOT LIKE :roleSA')
            ->setParameter('roles', '%'."".'%')
            ->setParameter('roleA', '%'."ROLE_ADMIN".'%')
            ->setParameter('roleSA', '%'."ROLE_SUPER_ADMIN".'%')
            ->orderBy('u.createdAt', 'desc');

        if ($search->getEmail())
            $qb->andWhere('u.email LIKE :email')->setParameter('email', '%'.$search->getEmail().'%');

        if ($search->getPhone())
            $qb->andWhere('u.phone LIKE :phone')->setParameter('phone', '%'.$search->getPhone().'%');

        if ($search->getCity())
            $qb->andWhere('location.name = :city')->setParameter('city', $search->getCity());

        return $qb;
    }

    public function findOneByEmail(string $email): object
    {
        return $this->findOneBy(['email' => $email]);
    }

    /**
     * Requête permettant de récupérer un utilisateur pour le login.
     *
     * @throws NonUniqueResultException
     */
    public function findForAuth(string $username): ?User
    {
        return $this->createQueryBuilder('u')
            ->where('LOWER(u.email) = :username')
            ->orWhere('LOWER(u.username) = :username')
            ->setMaxResults(1)
            ->setParameter('username', mb_strtolower($username))
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Cherche un utilisateur pour l'oauth.
     *
     * @throws NonUniqueResultException
     */
    public function findForOauth(string $service, ?string $serviceId, ?string $email): ?User
    {
        if (null === $serviceId || null === $email) {
            return null;
        }

        return $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->orWhere("u.{$service}Id = :serviceId")
            ->setMaxResults(1)
            ->setParameters([
                'email' => $email,
                'serviceId' => $serviceId,
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return User[]
     */
    public function clean(): array
    {
        $query = $this->createQueryBuilder('u')
            ->where('u.deleteAt IS NOT NULL')
            ->andWhere('u.deleteAt < NOW()');

        /** @var User[] $users */
        $users = $query->getQuery()->getResult();
        $query->delete(User::class, 'u')->getQuery()->execute();

        return $users;
    }

    public function findAllUsers()
    {
        $qb = $this->createQueryBuilder('u');

        $qb->where($qb->expr()->isNull('u.deleteAt'))
            ->andWhere('u.deleteAt IS NULL')
            ->andWhere('u.confirmationToken IS NOT NULL')
            ->andWhere('u.roles LIKE :roles')
            ->andWhere('u.roles NOT LIKE :roleA')
            ->andWhere('u.roles NOT LIKE :roleSA')
            ->setParameter('roles', '%'."".'%')
            ->setParameter('roleA', '%'."ROLE_ADMIN".'%')
            ->setParameter('roleSA', '%'."ROLE_SUPER_ADMIN".'%')
            ->orderBy('u.createdAt', 'desc');

        return $qb->getQuery()->getResult();
    }

    /**
     * Récupère les derniers comptes utilisateurs active
     *
     * @return int|mixed|string
     */
    public function getLastClients()
    {
        $qb = $this->createQueryBuilder('u');

        $qb->where($qb->expr()->isNull('u.deleteAt'))
            ->andWhere('u.confirmationToken IS NULL')
            ->andWhere('u.roles LIKE :roles')
            ->andWhere('u.roles NOT LIKE :roleA')
            ->andWhere('u.roles NOT LIKE :roleSA')
            ->setParameter('roles', '%'."".'%')
            ->setParameter('roleA', '%'."ROLE_ADMIN".'%')
            ->setParameter('roleSA', '%'."ROLE_SUPER_ADMIN".'%')
            ->orderBy('u.createdAt', 'desc')
            ->setMaxResults(5);

        return $qb->getQuery()->getResult();
    }

    /**
     * Récupère le nombres total d'utilisateurs active
     *
     * @return QueryBuilder|int|mixed|string
     */
    public function getUserNumber()
    {
        $qb = $this->createQueryBuilder('u')
            ->select('count(u.id)');

        $qb->where($qb->expr()->isNull('u.deleteAt'))
            ->andWhere('u.roles LIKE :roles')
            ->andWhere('u.roles NOT LIKE :roleA')
            ->andWhere('u.roles NOT LIKE :roleSA')
            ->setParameter('roles', '%'."".'%')
            ->setParameter('roleA', '%'."ROLE_ADMIN".'%')
            ->setParameter('roleSA', '%'."ROLE_SUPER_ADMIN".'%');

        return $qb->getQuery()->getSingleScalarResult();
    }
}
