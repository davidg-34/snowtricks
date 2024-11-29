<?php

namespace App\Repository;

use App\Entity\Comments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comments>
 *
 * @method Comments|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comments|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comments[]    findAll()
 * @method Comments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comments::class);
    }

    public function findCommentsByTrickSlug(string $trickSlug, int $page = 1, int $limit = 10): array
    {
        $offset = ($page - 1) * $limit;  // Calcul de l'offset
        return $this->createQueryBuilder('c')
            ->innerJoin('c.tricks', 't')
            ->where('t.slug = :trickSlug')
            ->setParameter('trickSlug', $trickSlug)
            ->orderBy('c.createdAt', 'ASC')  // Trier par ordre croissant de la date de création
            ->setFirstResult($offset)  // Définit l'offset pour la pagination.
            ->setMaxResults($limit)  // Limite le nombre maximum de résultats à retourner.
            ->getQuery()  // Convertit le QueryBuilder en une requête.
            ->getResult();  // Exécute la requête et retourne les résultats.
    }
}

//    /**
//     * @return Comments[] Returns an array of Comments objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Comments
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
