<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    // /**
    //  * @return Comment[] Returns an array of Comment objects
    //  */

public function getAllCommentsOrderDate()
{
    # Cette méthode récupère tous kes commentaires par ordre de date de création
    return $this->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
}

public function getCommentsByUser(User $user)
{
    // createQueryBuilder() est une méthode permettant de construire une requête SQL
    return $this->createQueryBuilder('c')
                ->andWhere('c.author = :val') // permet de donner une condition à la requete
                ->setParameter('val', $user) // permet de donner une valeur à la variable donnée dans le andWhere (:val)
                ->getQuery() // créer la requete
                ->getResult(); // renvoi le resultat de la requete
}

    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Comment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
