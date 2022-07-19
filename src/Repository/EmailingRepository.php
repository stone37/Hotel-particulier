<?php

namespace App\Repository;

use App\Entity\Emailing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Emailing>
 *
 * @method Emailing|null find($id, $lockMode = null, $lockVersion = null)
 * @method Emailing|null findOneBy(array $criteria, array $orderBy = null)
 * @method Emailing[]    findAll()
 * @method Emailing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Emailing::class);
    }

    public function add(Emailing $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Emailing $entity, bool $flush = false): void
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

    public function getAdmin(string $type): QueryBuilder
    {
        return $this->createQueryBuilder('e')
            ->where('e.groupe = :groupe')
            ->setParameter('groupe', $type)
            ->orderBy('e.createdAt', 'desc');
    }
}
