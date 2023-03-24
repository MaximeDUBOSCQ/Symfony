<?php

namespace App\Controller;

use App\Entity\Employe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\ConnexionType;
use Symfony\Component\HttpFoundation\Session\Session;

class ConnexionController extends AbstractController
{
    #[Route('/connexion', name: 'app_connexion')]
    public function ConnexionAction(Request $request, ManagerRegistry $doctrine, $employe = null) 
    {
        if ($employe == null) {
            $employe = new Employe();
        }
        $form = $this->createForm(ConnexionType::class, $employe);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $login = $employe->getLogin();
            $mdp = md5($employe->getMdp() . 5);
            $employe = $doctrine->getManager()->getRepository(Employe::class)->findByLoginAndMdp($login, $mdp);
            if($employe == null){
                return $this->redirectToRoute('app_connexion');
            }
            else{
                $session = new Session();
                $session->set('employe', $employe);
                if ($employe->getStatut()==0){
                    return $this->redirectToRoute('app_afficher_les_formations_pour_sinscrire');
                }
                else{
                    return $this->redirectToRoute('app_afficher_les_inscriptions_et_formations');
                }
            }
        }
    return $this->render('connexion/connexion.html.twig', array('form'=>$form->createView()));
    }

    #[Route('/deconnexion', name: 'app_deconnexion')]
    public function AjoutFormationAction(Session $session) 
    {
        $session->set('employe', null);
        return $this->redirectToRoute('app_afficher_les_inscriptions_et_formations');
    }
}