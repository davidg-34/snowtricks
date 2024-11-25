<?php

namespace App\Repository;

use App\Entity\Tricks;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tricks>
 *
 * @method Tricks|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tricks|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tricks[]    findAll()
 * @method Tricks[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TricksRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tricks::class);
    }

    public function findWithPagination(int $page = 1, int $limit = 10): array
    {
    $offset = ($page - 1) * $limit;  // Calcul de l'offset 
    return $this->createQueryBuilder('t')  // Crée un QueryBuilder avec l'alias t pour l'entité
        ->setFirstResult($offset)  // Définit l'offset pour la pagination.
        ->setMaxResults($limit)  // Limite le nombre maximum de résultats à retourner.
        ->getQuery()  // Convertit le QueryBuilder en une requête.
        ->getResult();  // Exécute la requête et retourne les résultats.
    }

//    /**
//     * @return Tricks[] Returns an array of Tricks objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Tricks
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
