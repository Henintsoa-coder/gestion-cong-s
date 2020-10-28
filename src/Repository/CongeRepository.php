<?php

namespace App\Repository;

use App\Entity\Conge;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Conge|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conge|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conge[]    findAll()
 * @method Conge[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CongeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conge::class);
    }

    public function findAllDESC(){
        return $this->createQueryBuilder('c')
        ->orderBy('c.id', 'DESC')
        ->getQuery()
        ->getResult();
    }

    //les demandes à traiter par le modérateur
    public function findAllNotViseeDESC(){
        return $this->createQueryBuilder('c')
        ->andWhere('c.vue = null')
        ->andWhere('c.vue = false')
        ->orderBy('c.id', 'DESC')
        ->getQuery()
        ->getResult();
    }

    //les demandes à traiter par l'admininstrateur
    public function findAllViseeDESC(){
        return $this->createQueryBuilder('c')
        ->andWhere('c.vue = true')
        ->orderBy('c.id', 'DESC')
        ->getQuery()
        ->getResult();
    }

    //les demandes vue par un ["ROLE_USER"]
    public function findOneUserById($utilisateurId): ?Conge
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.utilisateur = :val')
            ->setParameter('val', $utilisateurId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    

    // /**
    //  * Returns an array of Conges objects
    //  */
    
    public function findByUtilisateurId($utilisateurId)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.utilisateur = :val')
            ->setParameter('val', $utilisateurId)
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Conge
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
