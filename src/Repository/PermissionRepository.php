<?php

namespace App\Repository;

use App\Entity\Permission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Permission|null find($id, $lockMode = null, $lockVersion = null)
 * @method Permission|null findOneBy(array $criteria, array $orderBy = null)
 * @method Permission[]    findAll()
 * @method Permission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PermissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Permission::class);
    }

    public function findAllDESC(){
        return $this->createQueryBuilder('p')
        ->orderBy('p.id', 'DESC')
        ->getQuery()
        ->getResult();
    }

    //les demandes à traiter par le modérateur
    public function findAllNotViseeDESC(){
        return $this->createQueryBuilder('p')
        ->andWhere('p.vue = null')
        ->andWhere('p.vue = false')
        ->orderBy('p.id', 'DESC')
        ->getQuery()
        ->getResult();
    }

    //les demandes à traiter par l'admininstrateur 
    //requêtes SQL à revoir
    public function findAllViseeDESC(){
        return $this->createQueryBuilder('p')
        ->andWhere('p.vue = true')
        ->orderBy('p.id', 'DESC')
        ->getQuery()
        ->getResult();
    }

    // /**
    //  * Returns an array of Permissions objects
    //  */
    
    public function findByUtilisateurId($utilisateurId)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.utilisateur = :val')
            ->setParameter('val', $utilisateurId)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Permission[] Returns an array of Permission objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Permission
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
