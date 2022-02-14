<?php

namespace App\Controller;

use App\Entity\Sondage;
use App\Form\SondageType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SondageController extends AbstractController
{
    #[Route('/', name: 'accueil')]
    public function index(ManagerRegistry $manager): Response
    {

        return $this->render('sondage/index.html.twig', [
            'sondageList' => $manager->getRepository(Sondage::class)->findAll(),
        ]);
    }

    #[Route("/sondage/add", name:"sondage_add")]
    public function add(ManagerRegistry $manager, Request $request):Response
    {
        $sondage = new Sondage;
        $form = $this->createForm(SondageType::class, $sondage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $manager->getManager();
            $em->persist($sondage);
            $em->flush();
            // TODO: Générer la redirection vers la création de questions
            return $this->redirectToRoute("question_add", ["id" => $sondage->getId()]);
            throw new \Exception("Générer la redirection vers la création de questions");
            
        }

        return $this->render("sondage/add.html.twig", [
            'form' => $form->createView()
        ]);
    }

    #[Route('/sondage/{id}', name:"sondage_single")]
    public function single(Sondage $sondage):Response
    {
        // Pour chacune des questions je vais regarder les réponses
        // Pour chacune de ces réponse j'enregistre celle qui a le vote le plus élevé
        // Je passe l'information au template pour changer le style de la réponse la plus haute

        // Tableau stockant les id des réponses aux votes les plus élevés
        $reponseIds = array();
        // On parcourt nos questions pour accéder aux réponses
        foreach ($sondage->getQuestions() as $question) {
            // Valeur du vote le plus élevé
            $voteMax = 0;
            // Id de la réponse avec le vote le plus élevé
            $reponseId = 0;
            foreach ($question->getReponses() as $reponse) {
                if ($reponse->getVote() > $voteMax) {
                    // On stocke le vote max pour comparer le vote de toutes les réponses d'une question
                    $voteMax = $reponse->getVote();
                    // On stocke l'id de la réponse ayant le vote max
                    $reponseId = $reponse->getId();
                }
            }
            // On stocke l'id de la réponse ayant le vote max dans le tableau des ids
            $reponseIds[] = $reponseId;
        }
        return $this->render('/sondage/single.html.twig', [
            'sondage' => $sondage,
            'reponseIds' => $reponseIds
        ]);
    }
}
