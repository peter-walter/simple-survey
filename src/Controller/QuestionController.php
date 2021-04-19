<?php

namespace App\Controller;

use App\Entity\Question;
use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Regex;

class QuestionController extends AbstractController
{
  /**
   * Display a single question on the screen, handle the response
   * @Route("/question/{id}", name="question", requirements={"id"="\d+"})
   * @param QuestionRepository $questionRepository
   * @param SessionInterface $session
   * @param Request $request
   * @param int $id
   * @return Response
   * @throws \Exception
   */
  public function index(QuestionRepository $questionRepository, SessionInterface $session, Request $request, int $id): Response
  {
    $questionRepository->attachSession($session);
    $question = $questionRepository->find($id);

    if (!$question) {
      throw new \Exception('unable to retrieve question');
    }

    $form = $this->generateForm($question);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $question->setAnswer($form->getData()['answer']);
      $questionRepository->persist($question);
      $questionRepository->flush();

      $nextQuestion = $questionRepository->findNextRequired();
      if (is_null($nextQuestion)) {
        // survey in finished, go to summary
        return $this->redirectToRoute('summary');
      } else {
        // go to next question
        return $this->redirectToRoute('question', [
          'id' => $nextQuestion->getId(),
        ]);
      }
    }

    return $this->render('question/index.html.twig', [
      'question' => $question,
      'form' => $form->createView(),
    ]);
  }

  /**
   * Programmatically generate the form for the survey question, including validation criteria
   * @param Question $question
   * @return FormInterface
   * @throws \Exception
   */
  private function generateForm(Question $question): FormInterface
  {
    switch ($question->getType()) {

      case Question::SINGLE_CHOICE:
        $form = $this->createFormBuilder()
          ->add('answer', ChoiceType::class, [
            'data' => $question->getAnswer(),
            'label' => $question->getQuestion(),
            'help' => 'Please select one option',
            'choices' => array_flip($question->getChoices()),
            'expanded' => true,
            'multiple' => false,
            'constraints' => [
              new Choice([
                'choices' => array_keys($question->getChoices()),
                'multiple' => false,
              ]),
            ],
          ])
          ->add('next', SubmitType::class)
          ->getForm();
        break;

      case Question::MULTIPLE_CHOICE:
        $form = $this->createFormBuilder()
          ->add('answer', ChoiceType::class, [
            'data' => $question->getAnswer(),
            'label' => $question->getQuestion(),
            'help' => 'Please select one or more options',
            'choices' => array_flip($question->getChoices()),
            'expanded' => true,
            'multiple' => true,
            'constraints' => [
              new Choice([
                'choices' => array_keys($question->getChoices()),
                'multiple' => true,
                'min' => 1,
              ]),
            ],
          ])
          ->add('next', SubmitType::class)
          ->getForm();
        break;

      case Question::FREE_TEXT:
        $form = $this->createFormBuilder()
          ->add('answer', TextareaType::class, [
            'data' => $question->getAnswer(),
            'label' => $question->getQuestion(),
            'constraints' => [
              new Regex('/(\S+\s+){4,}\S+/', 'Response should be at least 5 words'),
            ]
          ])
          ->add('next', SubmitType::class)
          ->getForm();
        break;
      default:
        throw new \Exception('invalid question type');
    }
    return $form;
  }
}
