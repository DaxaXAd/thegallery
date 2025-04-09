<?php

namespace App\Repository;

use App\Entity\Like;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Like>
 */
class LikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Like::class);
    }

    /**
     * Retourne le nombre total de likes pour un post donné.
     */
    public function totalLike($postId): int // On définit le type de retour de la méthode comme un entier
    {    
        $qb = $this->createQueryBuilder('l') // On crée un QueryBuilder pour l'entité Like
            ->select('count(l.id)') // On compte le nombre de likes
            ->where('l.post = :postId') // On filtre par post
            ->setParameter('postId', $postId); // On définit le paramètre par postId
        // On exécute la requête et on récupère le résultat
        // getSingleScalarResult() renvoie un seul résultat, ici le nombre total de likes
        return $qb->getQuery()->getSingleScalarResult(); // renvoie le résultat avec getSingleScalarResult 
    }    
    //    /**
    //     * @return Like[] Returns an array of Like objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Like
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
