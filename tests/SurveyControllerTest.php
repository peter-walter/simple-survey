<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SurveyControllerTest extends WebTestCase
{
  public function testSurveyHomePage(): void
  {
    $client = static::createClient();
    $crawler = $client->request('GET', '/');

    $this->assertResponseIsSuccessful();
    $this->assertSelectorTextContains('button', "Start survey");
  }

  public function testSurveyStarts(): void
  {
    $client = static::createClient();
    $crawler = $client->request('GET', '/');
    $crawler = $client->submitForm('Start survey');

    $this->assertResponseRedirects('/question/0');
  }
}
