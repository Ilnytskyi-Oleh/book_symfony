<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }


    public function findPaginated(int $page = 1, int $limit = 10)
    {
        $query = $this->createQueryBuilder('b')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery();

        $paginator = new Paginator($query);

        return [
            'data' => iterator_to_array($paginator),
            'total' => $paginator->count(),
            'current_page' => $page,
            'per_page' => $limit
        ];
    }

    public function findByAuthorLastName(string $lastName): array
    {
        return $this->createQueryBuilder('b')
            ->join('b.authors', 'a')
            ->where('a.lastName LIKE :lastName')
            ->setParameter('lastName', '%' . $lastName . '%')
            ->getQuery()
            ->getResult();
    }
}

