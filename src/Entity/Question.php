<?php


namespace App\Entity;


class Question
{
  const FREE_TEXT = 1;
  const SINGLE_CHOICE = 2;
  const MULTIPLE_CHOICE = 3;

  /**
   * @var string
   */
  private $question;
  /**
   * @var int
   */
  private $type;
  /**
   * @var array
   */
  private $choices = [];
  /**
   * @var int
   */
  private $id = null;

  /**
   * @var mixed
   */
  private $answer = null;
  /**
   * @var array
   */
  private $conditions = [];

  /**
   * @return int|null
   */
  public function getId(): ?int
  {
    return $this->id;
  }

  /**
   * Question constructor.
   * @param string $question
   * @param int $type
   * @param array $conditions
   * @param array $choices
   */
  public function __construct(string $question, int $type, array $conditions = [], array $choices = [])
  {
    $this->question = $question;
    $this->type = $type;
    $this->conditions = $conditions;
    $this->choices = $choices;
  }

  /**
   * @return string
   */
  public function getQuestion(): string
  {
    return $this->question;
  }

  /**
   * @return int
   */
  public function getType(): int
  {
    return $this->type;
  }

  /**
   * @return array
   */
  public function getChoices(): array
  {
    return $this->choices;
  }

  /**
   * @param $answer
   */
  public function setAnswer($answer): void
  {
    $this->answer = $answer;
  }

  /**
   * Return the raw answer
   * @return mixed
   */
  public function getAnswer()
  {
    return $this->answer;
  }

  /**
   * Return the answer(s) as a string
   * @return string
   */
  public function getAnswerString(): string
  {
    if (is_null($this->answer))
      return '';

    switch ($this->type) {
      case self::FREE_TEXT:
        return $this->answer;

      case self::SINGLE_CHOICE:
        return $this->choices[$this->answer];

      case self::MULTIPLE_CHOICE:
        if (!is_array($this->answer))
          return '';

        $responses = [];
        foreach ($this->answer as $answer) {
          $responses[] = $this->choices[$answer];
        }
        return join(', ', $responses);

      default:
        return '';
    }
  }

  /**
   * Return the answer(s) as an array of strings
   * @return array
   */
  public function getAnswerArray(): array
  {
    if (is_null($this->answer)) return [];

    return explode(', ', $this->getAnswerString());
  }

  /**
   * @return bool
   */
  public function hasAnswer(): bool
  {
    return strlen($this->getAnswerString()) > 0;
  }

  /**
   * @param int $id
   */
  public function setId(int $id)
  {
    $this->id = $id;
  }

  /**
   * Return array of conditions for this question to be answerable
   * @return array
   */
  public function getConditions(): array
  {
    return $this->conditions;
  }

  /**
   * @return bool
   */
  public function hasConditions(): bool
  {
    return count($this->getConditions()) > 0;
  }


}