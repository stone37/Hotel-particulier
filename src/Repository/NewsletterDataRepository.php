<?php

namespace App\Repository;

use App\Entity\NewsletterData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NewsletterData>
 *
 * @method NewsletterData|null find($id, $lockMode = null, $lockVersion = null)
 * @method NewsletterData|null findOneBy(array $criteria, array $orderBy = null)
 * @method NewsletterData[]    findAll()
 * @method NewsletterData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsletterDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewsletterData::class);
    }

    public function add(NewsletterData $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(NewsletterData $entity, bool $flush = false): void
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

    public function getNumber(): ?int
    {
        $qb = $this->createQueryBuilder('nd')
            ->select('count(nd.id)');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
