<?php

namespace App\Tests\Entity;

use App\Entity\Question;
use PHPUnit\Framework\TestCase;

class QuestionTest extends TestCase
{
  public function testFreeTextQuestion(): void
  {
    $question = new Question('What is your name?', Question::FREE_TEXT);

    $this->assertNull($question->getId());
    $this->assertEquals('What is your name?', $question->getQuestion());
    $this->assertEquals([], $question->getChoices());
    $this->assertEquals([], $question->getAnswerArray());
    $this->assertNull($question->getAnswer());
    $this->assertEmpty($question->getAnswerString());
    $this->assertFalse($question->hasAnswer());
    $this->assertFalse($question->hasConditions());
    $this->assertEquals(Question::FREE_TEXT, $question->getType());
  }

  public function testSingleChoiceQuestion(): void
  {
    $question = new Question('What is your favourite colour?', Question::SINGLE_CHOICE, [], ['Red', 'Green', 'Blue']);

    $this->assertNull($question->getId());
    $this->assertEquals('What is your favourite colour?', $question->getQuestion());
    $this->assertEquals(['Red', 'Green', 'Blue'], $question->getChoices());
    $this->assertEquals([], $question->getAnswerArray());
    $this->assertEmpty($question->getAnswerString());
    $this->assertFalse($question->hasAnswer());
    $this->assertFalse($question->hasConditions());
    $this->assertEquals(Question::SINGLE_CHOICE, $question->getType());
  }

  public function testSetSingleAnswer(): void
  {
    $question = new Question('What is your favourite colour?', Question::SINGLE_CHOICE, [], ['Red', 'Green', 'Blue']);
    $question->setAnswer(0);

    $this->assertTrue($question->hasAnswer());
    $this->assertEquals(0, $question->getAnswer());
    $this->assertEquals(['Red'], $question->getAnswerArray());
    $this->assertEquals('Red', $question->getAnswerString());
  }

  public function testSetMultipleAnswer(): void
  {
    $question = new Question('What is your favourite colour?', Question::MULTIPLE_CHOICE, [], ['Red', 'Green', 'Blue']);
    $question->setAnswer([0, 2]);

    $this->assertTrue($question->hasAnswer());
    $this->assertEquals([0, 2], $question->getAnswer());
    $this->assertEquals(['Red', 'Blue'], $question->getAnswerArray());
    $this->assertEquals('Red, Blue', $question->getAnswerString());
  }

  public function testSetConditions(): void
  {
    $question = new Question('What is your favourite colour?', Question::MULTIPLE_CHOICE, ['id' => 0, 'answer' => 'Peter'], ['Red', 'Green', 'Blue']);

    $this->assertTrue($question->hasConditions());
    $this->assertEquals(['id' => 0, 'answer' => 'Peter'], $question->getConditions());
  }
}