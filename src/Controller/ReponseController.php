<?php

namespace App\Controller;

use App\Entity\Reponse;
use App\Entity\Sondage;
use App\Form\ReponseType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReponseController extends AbstractController
{
    #[Route('/reponses', name: 'reponses')]
    public function index(ManagerRegistry $manager): Response
    {
        return $this->render('reponse/index.html.twig', [
            'reponseList' => $manager->getRepository(Reponse::class)->findAll(),
        ]);
    }

    #[Route('/reponse/add', name:'reponse_add')]
    public function add (Request $request, ManagerRegistry $manager): Response
    {
        $reponse = new Reponse;
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $manager->getManager();
            $reponse->setVote(0);
            $em->persist($reponse);
            $em->flush();

            return $this->redirectToRoute('reponses');
        }

        return $this->render('reponse/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/reponse/{id}/update', name:'reponse_update')]
    public function update (Reponse $reponse, ManagerRegistry $manager, Request $request):Response
    {
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $manager->getManager();
            $em->persist($reponse);
            $em->flush();

            return $this->redirectToRoute('reponses');
        }

        return $this->render('reponse/update.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route("reponse/update/{sondage}/{reponse}", name:"update_vote")]
    public function updateVote (Sondage $sondage, Reponse $reponse, ManagerRegistry $manager):Response
    {
        $reponse->setVote($reponse->getVote() +1);

        $manager->getManager()->persist($reponse);
        $manager->getManager()->flush();

        // return $this->redirectToRoute('sondage_single', ["id" => $reponse->getQuestion()->getSondage()->getId()]);
        return $this->redirectToRoute('sondage_single', ["id" => $sondage->getId()]);
    }
}
