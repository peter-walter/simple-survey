<?php

namespace App\Controller;

use App\Entity\Question;
use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class SummaryController extends AbstractController
{
  /**
   * Display summary of the survey responses for this user
   * @Route("/summary", name="summary")
   * @param QuestionRepository $questionRepository
   * @param SessionInterface $session
   * @return Response
   */
    public function index(QuestionRepository $questionRepository, SessionInterface $session): Response
    {
      $questionRepository->attachSession($session);
      $questions = $questionRepository->findAll();
      $responses = [];

      // extract information to display to user
      /** @var Question $question */
      foreach ($questions as $question) {
        if ($question->hasAnswer())
          $responses[$question->getId()] = [
            'id' => $question->getId(),
            'question' => $question->getQuestion(),
            'answer' => $question->getAnswerString(),
          ];
      }

      return $this->render('summary/index.html.twig', [
        'responses' => $responses,
      ]);
    }
}
