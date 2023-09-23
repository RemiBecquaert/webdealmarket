<?php

namespace App\Repository;

use App\Entity\MarqueProduit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @extends ServiceEntityRepository<MarqueProduit>
 *
 * @method MarqueProduit|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarqueProduit|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarqueProduit[]    findAll()
 * @method MarqueProduit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarqueProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarqueProduit::class);
    }

    public function save(MarqueProduit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MarqueProduit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findForPagination():Query{
        $qb = $this->createQueryBuilder('p')
        ->orderBy('p.libelle', 'ASC');
        return $qb->getQuery();
    }
}
