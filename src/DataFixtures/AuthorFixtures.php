<?php

namespace App\DataFixtures;

use App\Entity\Author;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AuthorFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $author = new Author();
        $author->setFirstName('Іван');
        $author->setLastName('Петренко');
        $author->setMiddleName('Іванович');
        $manager->persist($author);

        $manager->flush();
    }
}

