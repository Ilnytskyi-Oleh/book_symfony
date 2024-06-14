<?php

namespace App\Tests\Controller;

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthorControllerTest extends WebTestCase
{
    protected static function getKernelClass(): string
    {
        return Kernel::class;
    }

    public function testCreateAuthor(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/authors',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'firstName' => 'Тест',
                'lastName' => 'Автор',
                'middleName' => 'Тестович'
            ])
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testGetAuthors(): void
    {
        $client = static::createClient();
        $client->request('GET', '/authors?page=1&limit=10');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }
}
