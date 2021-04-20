<?php

namespace App\Tests\Repository;

use App\Entity\Question;
use App\Repository\QuestionRepository;
use PHPUnit\Framework\TestCase;

class QuestionRepositoryTest extends TestCase
{
  public function testRepositoryReturnsACopy(): void {
    $questionRepository = new QuestionRepository();

    $q1 = new Question('What is your name?', Question::FREE_TEXT);
    $id1 = $questionRepository->persist($q1);

    $this->assertEquals($q1, $questionRepository->find($id1));
    $this->assertNotSame($q1, $questionRepository->find($id1));
  }

  public function testRepositoryReturnsNullWhenNotFound(): void {
    $questionRepository = new QuestionRepository();

    $q1 = new Question('What is your name?', Question::FREE_TEXT);
    $id1 = $questionRepository->persist($q1);

    $this->assertEquals($q1, $questionRepository->find($id1));
    $this->assertNull($questionRepository->find($id1+1));
  }

  public function testBasicRepositoryStorageAndRetrieval(): void
  {
    $questionRepository = new QuestionRepository();

    $q1 = new Question('What is your name?', Question::FREE_TEXT);
    $id1 = $questionRepository->persist($q1);

    $q2 = new Question('What is your favourite colour?', Question::SINGLE_CHOICE, [], ['Red', 'Green', 'Blue']);
    $id2 = $questionRepository->persist($q2);

    $q3 = new Question('What are your favourite shapes?', Question::MULTIPLE_CHOICE, [], ['Square', 'Circle', 'Triangle', 'Rectangle']);
    $id3 = $questionRepository->persist($q3);

    $this->assertEquals($q1, $questionRepository->find($id1));
    $this->assertEquals(Question::FREE_TEXT, $questionRepository->find($id1)->getType());
    $this->assertEquals($q2, $questionRepository->find($id2));
    $this->assertEquals(Question::SINGLE_CHOICE, $questionRepository->find($id2)->getType());
    $this->assertEquals($q3, $questionRepository->find($id3));
    $this->assertEquals(Question::MULTIPLE_CHOICE, $questionRepository->find($id3)->getType());
  }

  public function testRepositoryNavigation(): void
  {
    $questionRepository = new QuestionRepository();

    $q1 = new Question('What is your favourite colour?', Question::MULTIPLE_CHOICE, [], ['Red', 'Green', 'Blue']);
    $id1 = $questionRepository->persist($q1);

    $q2 = new Question('Do you really like blue?', Question::SINGLE_CHOICE, ['id' => $id1, 'answer' => 'Blue'], ['Yes', 'No']);
    $questionRepository->persist($q2);

    $q3 = new Question('Do you really like green?', Question::SINGLE_CHOICE, ['id' => $id1, 'answer' => 'Green'], ['Yes', 'No']);
    $questionRepository->persist($q3);

    $q4 = new Question('Do you really like red?', Question::SINGLE_CHOICE, ['id' => $id1, 'answer' => 'Red'], ['Yes', 'No']);
    $questionRepository->persist($q4);

    $this->assertEquals($q1, $qFirst = $questionRepository->findFirst());
    $qFirst->setAnswer([0, 2]);
    $questionRepository->persist($qFirst);

    $this->assertEquals($q2, $qNext = $questionRepository->findNextRequired());
    $qNext->setAnswer(0);
    $questionRepository->persist($qNext);

    // NB: $q3 should not be returned yet as it does not match the conditions

    $this->assertEquals($q4, $qNext = $questionRepository->findNextRequired());
    $qNext->setAnswer(1);
    $questionRepository->persist($qNext);

    // no more questions
    $this->assertNull($qNext = $questionRepository->findNextRequired());

    // now say you like green
    $qFirst->setAnswer([1]);
    $questionRepository->persist($qFirst);

    $this->assertEquals($q3, $qNext = $questionRepository->findNextRequired());
    $qNext->setAnswer(0);
    $questionRepository->persist($qNext);

    // no more questions
    $this->assertNull($qNext = $questionRepository->findNextRequired());
  }
}