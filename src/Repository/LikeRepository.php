<?php

namespace App\Repository;

use App\Entity\Like;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Like>
 *
 * @method Like|null find($id, $lockMode = null, $lockVersion = null)
 * @method Like|null findOneBy(array $criteria, array $orderBy = null)
 * @method Like[]    findAll()
 * @method Like[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Like::class);
    }
        public function findOneByUserAndRecipe($userId, $recipeId): ?Like
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.User = :user')
            ->andWhere('l.Recipe = :recipe')
            ->setParameter('user', $userId)
            ->setParameter('recipe', $recipeId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findLikeByRecipeId($recipeId): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.Recipe = :recipe')
            ->setParameter('recipe', $recipeId)
            ->getQuery()
            ->getResult();
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
