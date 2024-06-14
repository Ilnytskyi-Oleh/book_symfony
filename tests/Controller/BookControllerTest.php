<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookControllerTest extends WebTestCase
{
    public function testCreateBook(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/books',
            [],
            [
                'image' => new \Symfony\Component\HttpFoundation\File\UploadedFile(
                    __DIR__.'/../Resources/test.jpg',
                    'test.jpg',
                    'image/jpeg',
                    null
                )
            ],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'title' => 'Тестова Книга',
                'description' => 'Тестовий Опис',
                'publishedAt' => '2023-01-01',
                'authors' => [1]
            ])
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testGetBooks(): void
    {
        $client = static::createClient();
        $client->request('GET', '/books?page=1&limit=10');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testGetBook(): void
    {
        $client = static::createClient();
        $client->request('GET', '/books/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testSearchBooks(): void
    {
        $client = static::createClient();
        $client->request('GET', '/books/search?author=Автор');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testUpdateBook(): void
    {
        $client = static::createClient();
        $client->request(
            'PUT',
            '/books/1',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'title' => 'Оновлена Тестова Книга',
                'description' => 'Оновлений Тестовий Опис',
                'publishedAt' => '2023-01-01',
                'authors' => [1]
            ])
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }
}
