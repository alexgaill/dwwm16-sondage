<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Sondage;
use App\Form\Question\QuestionType;
use App\Form\Question\QuestionWithoutSondageType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    #[Route('/questions', name: 'questions')]
    public function index(ManagerRegistry $manager): Response
    {
        return $this->render('question/index.html.twig', [
            'questionList' => $manager->getRepository(Question::class)->findAll(),
        ]);
    }

    #[Route('/questions/add', name:"question_add")]
    #[Route('questions/add/{id}', name:"question_add")]
    public function add(Request $request, ManagerRegistry $manager, $id = 0): Response
    {
        $question = new Question;

        if ($id != 0) {
            $sondage = $manager->getRepository(Sondage::class)->find($id);
            $form = $this->createForm(QuestionWithoutSondageType::class, $question);
            $question->setSondage($sondage);
        } else {
            $form =$this->createForm(QuestionType::class, $question);
        }
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $em = $manager->getManager();
            $em->persist($question);
            $em->flush();

            return $this->redirectToRoute('questions');
        }

        return $this->render('question/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('question/{id}/update', name:'question_update')]
    public function update(Question $question, ManagerRegistry $manager, Request $request): Response
    {
        $form =$this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $em = $manager->getManager();
            $em->persist($question);
            $em->flush();

            return $this->redirectToRoute('questions');
        }

        return $this->render('question/update.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
