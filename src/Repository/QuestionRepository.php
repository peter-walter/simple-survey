<?php

namespace App\Repository;

use App\Entity\Question;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class QuestionRepository
{
  /**
   * @var array
   */
  private $persistedQuestions = [];
  /**
   * @var SessionInterface
   */
  private $session = null;

  /**
   * Attach session storage to this repository
   * @param SessionInterface $session
   */
  function attachSession(SessionInterface $session)
  {
    $this->session = $session;

    if ($this->session->has('questions')) {
      $this->persistedQuestions = unserialize($this->session->get('questions'));
    }
  }

  /**
   * Clear the persisted questions and session data
   */
  function clear()
  {
    $this->persistedQuestions = [];

    if ($this->session)
      $this->session->clear();
  }

  /**
   * Find a single Question by id
   * @param $id
   * @return Question|null
   */
  function find($id): ?Question
  {
    // return a clone of the object rather than a pointer to the object itself
    if (isset($this->persistedQuestions[$id])) {
      return clone($this->persistedQuestions[$id]);
    } else {
      return null;
    }
  }

  /**
   * Return an array of all Questions
   * @return array
   */
  function findAll(): array
  {
    return $this->persistedQuestions;
  }


  /**
   * Return the first Question
   * @return Question|null
   */
  function findFirst(): ?Question
  {
    return $this->find(array_keys($this->persistedQuestions)[0]);
  }

  /**
   * Return the next required question
   * NB: Not all questions need to be answered, depending on conditional logig
   * @return Question|null
   */
  function findNextRequired(): ?Question
  {
    /** @var Question $question */
    foreach ($this->persistedQuestions as $question) {
      // question already has an answer, skip over this
      if ($question->hasAnswer())
        continue;

      // question has no answer, and no conditions
      if (!$question->hasConditions())
        return $this->find($question->getId());

      // question has no answer, but has conditions
      $conditions = $question->getConditions();
      /** @var Question $referenceQuestion */
      $referenceQuestion = $this->persistedQuestions[$conditions['id']];
      $answers = $referenceQuestion->getAnswerArray();

      // question matches conditions
      if (in_array($conditions['answer'], $answers))
        return $this->find($question->getId());
    }

    return null;
  }

  /**
   * Persist object to the repository
   * @param Question $question
   * @return int
   */
  function persist(Question $question): int
  {
    // objects only get an id when we persist them
    if (is_null($question->getId())) {
      $question->setId(count($this->persistedQuestions));
    }

    $this->persistedQuestions[$question->getId()] = $question;

    return $question->getId();
  }

  /**
   * Flush the repository to session storage
   * @throws \Exception
   */
  function flush()
  {
    if ($this->session)
      $this->session->set('questions', serialize($this->persistedQuestions));
    else {
      throw new \Exception('cannot flush data before session is set');
    }
  }
}