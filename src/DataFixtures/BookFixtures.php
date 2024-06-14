<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BookFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $author = $manager->getRepository(Author::class)->find(1);

        $book = new Book();
        $book->setTitle('Тестова Книга');
        $book->setDescription('Тестовий Опис');
        $book->setPublishedAt(new \DateTime('2023-01-01'));
        $book->addAuthor($author);
        $manager->persist($book);

        $manager->flush();
    }
}


