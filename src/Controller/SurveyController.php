<?php

namespace App\Controller;

use App\Entity\Question;
use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class SurveyController extends AbstractController
{
  /**
   * Display the survey start page
   * @Route("/", name="survey")
   * @param Request $request
   * @param SessionInterface $session
   * @param QuestionRepository $questionRepository
   * @return Response
   * @throws \Exception
   */
  public function index(Request $request, SessionInterface $session, QuestionRepository $questionRepository): Response
  {
    $form = $this->createFormBuilder()
      ->add('start', SubmitType::class)
      ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $this->initSurvey($questionRepository, $session);

      return $this->redirectToRoute('question', [
        'id' => $questionRepository->findFirst()->getId(),
      ]);
    }

    return $this->render('survey/index.html.twig', [
      'form' => $form->createView(),
    ]);
  }

  /**
   * Initialise the survey
   * TODO: read this configuration from some external file
   * @param QuestionRepository $questionRepository
   * @param SessionInterface $session
   * @throws \Exception
   */
  private function initSurvey(QuestionRepository $questionRepository, SessionInterface $session): void
  {
    $questionRepository->attachSession($session);
    $questionRepository->clear();

    $questionRepository->persist(new Question(
      'Please tell us your age?',
      Question::SINGLE_CHOICE,
      [],
      [
        'Under 18',
        '18-30',
        '31-45',
        '46-60',
        '60+',
      ],
    ));
    $animalQuestionId = $questionRepository->persist(new Question(
      'From the following list, which of these animals are your favourite?',
      Question::MULTIPLE_CHOICE,
      [],
      [
        'Rabbit',
        'Cat',
        'Dog',
        'Goldfish',
      ],
    ));
    $questionRepository->persist(new Question(
      'What do you like about a Rabbit?',
      Question::FREE_TEXT,
      [
        'id' => $animalQuestionId,
        'answer' => 'Rabbit',
      ]
    ));
    $questionRepository->persist(new Question(
      'What do you like about a Cat?',
      Question::FREE_TEXT,
      [
        'id' => $animalQuestionId,
        'answer' => 'Cat',
      ]
    ));
    $questionRepository->persist(new Question(
      'What do you like about a Dog?',
      Question::FREE_TEXT,
      [
        'id' => $animalQuestionId,
        'answer' => 'Dog',
      ]
    ));
    $questionRepository->persist(new Question(
      'What do you like about a Goldfish?',
      Question::FREE_TEXT,
      [
        'id' => $animalQuestionId,
        'answer' => 'Goldfish',
      ]
    ));

    $questionRepository->flush();
  }
}
