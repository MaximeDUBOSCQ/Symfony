<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity\Inscription;
use App\Entity\Formation;
use App\Entity\Employe;
use App\Form\CreerFormationType;
use Symfony\Component\HttpFoundation\Request;


class AdministrateurController extends AbstractController
{
    #[Route('/creerFormation', name: 'app_creer_formation')]
    public function AjoutFormationAction(Session $session, Request $request, ManagerRegistry $doctrine, $formation = null) 
    {
        $retour = $doctrine->getManager()->getRepository(Employe::class)->verifConnexion($session, 1);
        if($retour != null){
            return $this->redirectToRoute($retour);
        }
        if ($session->get('employe')->getStatut()==1){
            if ($formation == null) {
                    $formation = new Formation();
            }
            $form = $this->createForm(CreerFormationType::class, $formation);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $em = $doctrine->getManager();
                $em->persist($formation);
                $em->flush();
                return $this->redirectToRoute('app_afficher_les_inscriptions_et_formations');
            }
            return $this->render('formation/ajoutFormation.html.twig', array('form'=>$form->createView()));
        }
    } 

    #[Route('/supprimerUneFormation/{id}', name: 'app_supprimer_une_formation')]
    public function supprimerFormationAction(Session $session, $id, ManagerRegistry $doctrine) 
    {
        $retour = $doctrine->getManager()->getRepository(Employe::class)->verifConnexion($session, 1);
        if($retour != null){
            return $this->redirectToRoute($retour);
        }
        if ($session->get('employe')->getStatut()==1){
            $formation = $doctrine->getManager()->getRepository(Formation::class)->find($id);
            if ($formation) {
                $inscription = $doctrine->getManager()->getRepository(Inscription::class)->verifInscription($id);
                if (count($inscription) == 0) {
                    $entityManager = $doctrine->getManager();
                    $entityManager->remove($formation);
                    $entityManager->flush();
                }
            }
            return $this->redirectToRoute('app_afficher_les_inscriptions_et_formations');
        }
    }

    #[Route('/afficherLesInscriptionsEtFormations', name: 'app_afficher_les_inscriptions_et_formations')]
    public function afficherLesInscriptionsEtFormationsAction(Session $session, ManagerRegistry $doctrine) 
    {
        $retour = $doctrine->getManager()->getRepository(Employe::class)->verifConnexion($session, 1);
        if($retour != null){
            return $this->redirectToRoute($retour);
        }
            $lesInscriptions = $doctrine->getManager()->getRepository(Inscription::class)->findAll();
            if(!$lesInscriptions){
                $messageInscription = "Pas d'inscriptions";
            }
            else{
                $messageInscription = null;
            }
            $lesFormations = $doctrine->getManager()->getRepository(Formation::class)->findAll();
            if(!$lesFormations){
                $messageFormation = "Pas de formations";
            }
            else{
                $messageFormation = null;
            }
            return $this->render('formation/interfaceAdmin.html.twig', array('lesInscriptions'=>$lesInscriptions, 'lesFormations'=>$lesFormations, 'messageInscription'=>$messageInscription, 'messageFormation'=>$messageFormation));
    }

    #[Route('/refuserUneFormation/{id}', name: 'app_refuser_une_formation')]
    public function refuserUneFormationAction(Session $session, $id, ManagerRegistry $doctrine) 
    {
        $retour = $doctrine->getManager()->getRepository(Employe::class)->verifConnexion($session, 1);
        if($retour != null){
            return $this->redirectToRoute($retour);
        }
        if ($session->get('employe')->getStatut()==1){
            $inscription = $doctrine->getManager()->getRepository(Inscription::class)->find($id);

            $inscription->setStatut("Refuser");

            $entityManager = $doctrine->getManager();
            $entityManager->persist($inscription);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_afficher_les_inscriptions_et_formations');
        }
    }

    #[Route('/accepterUneFormation/{id}', name: 'app_accepter_une_formation')]
    public function accepterUneFormationAction(Session $session, $id, ManagerRegistry $doctrine) 
    {
        $retour = $doctrine->getManager()->getRepository(Employe::class)->verifConnexion($session, 1);
        if($retour != null){
            return $this->redirectToRoute($retour);
        }
        if ($session->get('employe')->getStatut()==1){
            $inscription = $doctrine->getManager()->getRepository(Inscription::class)->find($id);

            $inscription->setStatut("Accepter");

            $entityManager = $doctrine->getManager();
            $entityManager->persist($inscription);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_afficher_les_inscriptions_et_formations');
        }
    }
}