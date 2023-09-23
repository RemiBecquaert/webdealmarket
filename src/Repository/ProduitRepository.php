<?php

namespace App\Repository;

use App\Entity\Produit;
use App\Entity\MarqueProduit;
use App\Entity\CategorieProduit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @extends ServiceEntityRepository<Produit>
 *
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    public function save(Produit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Produit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findForPagination(?CategorieProduit $categorie = null, ?MarqueProduit $marque = null):Query{
        $qb = $this->createQueryBuilder('p')
        ->orderBy('p.libelle', 'ASC');
        if($categorie){
            $qb->leftJoin('p.idCategorie', 'c')
            ->where($qb->expr()->eq('c.id', ':id'))
            ->setParameter('id', $categorie->getId());
        }
        if($marque){
            $qb->leftJoin('p.idMarque', 'm')
            ->where($qb->expr()->eq('m.id', ':id'))
            ->setParameter('id', $marque->getId());
        }
        return $qb->getQuery();

    }

/*    public function findForPagination(?Category $category = null): Query
    {
        $qb = $this->createQueryBuilder('a')
            ->orderBy('a.createdAt', 'DESC');

        if ($category) {
            $qb->leftJoin('a.categories', 'c')
                ->where($qb->expr()->eq('c.id', ':id'))
                ->setParameter('id', $category->getId());
        }

        return $qb->getQuery();
    }*/
//    /**
//     * @return Produit[] Returns an array of Produit objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Produit
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
