<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EvaluationControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'evaluation_index');
    }

    public function testAdd()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'evaluation_add');
    }

}
