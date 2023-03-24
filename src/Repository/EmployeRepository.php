<?php

namespace App\Repository;

use App\Entity\Employe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @extends ServiceEntityRepository<Employe>
 *
 * @method Employe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Employe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Employe[]    findAll()
 * @method Employe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmployeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employe::class);
    }

    public function save(Employe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Employe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByLoginAndMdp($login, $mdp) : ?Employe
    {
       return $this->createQueryBuilder('employe')
           ->andWhere('employe.login = :login')
           ->setParameter('login', $login)
           ->andWhere('employe.mdp = :mdp')
           ->setParameter('mdp', $mdp)
           ->getQuery()
           ->getOneOrNullResult()
       ;
    }

    public function verifConnexion(Session $session, $unStatut) {
        $retour = null;
        if ($session->get('employe') == null) {
            $retour = 'app_connexion';
        } elseif ($unStatut == 0 && $session->get('employe')->getStatut() != 0) {
            $retour = 'app_afficher_les_inscriptions_et_formations';
        } elseif ($unStatut == 1 && $session->get('employe')->getStatut() != 1) {
            $retour = 'afficherLesFormationsPourSInscrire';
        }
        return $retour;
    }

    public function findEmployeFormation($statut): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.statut = :statut')
            ->setParameter('statut', $statut)
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return Employe[] Returns an array of Employe objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Employe
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
