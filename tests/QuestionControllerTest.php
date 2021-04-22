<?php


namespace App\Tests;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class QuestionControllerTest extends WebTestCase
{
  static $client;

  public function setup(): void {
    self::$client = static::createClient();
    self::$client->request('GET', '/');
    self::$client->submitForm('Start survey');
  }

  public function testSingleChoiceQuestion(): void
  {
    $crawler = self::$client->request('GET', '/question/0');


    $this->assertResponseIsSuccessful();
    $this->assertGreaterThan(0, $crawler->filter('input[type=radio]')->count());
    $this->assertSelectorTextContains('button', "Next");
  }

  public function testSingleChoiceAnswerValidation(): void
  {
    self::$client->request('GET', '/question/0');

    $this->assertSelectorNotExists('input[checked]');
    self::$client->submitForm('Next');

    $this->assertResponseRedirects('/question/0');
  }

  public function testSingleChoiceAnswer(): void
  {
    self::$client->request('GET', '/question/0');

    self::$client->submitForm('Next', ['form[answer]' => 0]);

    $this->assertResponseRedirects('/question/1');
  }
}

