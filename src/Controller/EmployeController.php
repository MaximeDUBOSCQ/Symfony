<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity\Inscription;
use App\Entity\Formation;
use App\Entity\Employe;

class EmployeController extends AbstractController
{
    #[Route('/afficherLesFormationsPourSInscrire', name: 'app_afficher_les_formations_pour_sinscrire')]
    public function afficherLesFormationsPourSInscrireAction(Session $session, ManagerRegistry $doctrine) 
    {
        $retour = $doctrine->getManager()->getRepository(Employe::class)->verifConnexion($session, 0);
        if($retour != null){
            return $this->redirectToRoute($retour);
        }
            $lesFormations = $doctrine->getManager()->getRepository(Formation::class)->findOnlyNewer();
            if(!$lesFormations){
                $message = "Pas de formations disponible";
            }
            else{
                $message = null;
            }
            return $this->render('inscription/listeFormationPourSInscrire.html.twig', array('lesFormations'=>$lesFormations, 'message'=>$message));
    }

    #[Route('/creerInscription/{id}', name: 'app_creer_inscription')]
    public function AjoutInscriptionAction(Session $session, $id, ManagerRegistry $doctrine) 
    {
        if ($session->get('employe') == null){
            return $this->redirectToRoute('app_connexion');
        }
        if ($session->get('employe')->getStatut()==0){
            $employe = $doctrine->getManager()->getRepository(Employe::class)->find($session->get('employe')->getId());
            $formation = $doctrine->getManager()->getRepository(Formation::class)->find($id);
            $inscription = new Inscription();

            $inscription->setStatut("En cours");
            $inscription->setleEmploye($employe);
            $inscription->setLaFormation($formation);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($inscription);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_afficher_les_formations_pour_sinscrire');
        }
    }
}