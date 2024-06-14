<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/books')]
class BookController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(BookRepository $bookRepository, Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $result = $bookRepository->findPaginated($page, $limit);
        return $this->json($result);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {

        $title = $request->request->get('title');
        $description = $request->request->get('description');
        $publishedAt = $request->request->get('published_at');
        $authorIds = $request->request->all('authors');

        $book = new Book();
        $book->setTitle($title);
        $book->setDescription($description);
        $book->setPublishedAt(new \DateTime($publishedAt));

        $errors = $validator->validate($book);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], 400);
        }

        // Authors
        $authors = $em->getRepository(Author::class)->findBy(['id' => $authorIds]);

        foreach ($authors as $author) {
            $book->addAuthor($author);
        }

        // Image
        /** @var UploadedFile $image */
        $image = $request->files->get('image');
        if ($image) {

            $fileName = uniqid().'.'.$image->guessExtension();
            try {
                $image->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
                $book->setImage($fileName);
            } catch (FileException $e) {
                return $this->json(['error' => 'Failed to upload image'], 400);
            }
        }

        $em->persist($book);
        $em->flush();
        return $this->json($book, 201);
    }

    #[Route('/search', methods: ['GET'])]
    public function search(BookRepository $bookRepository, Request $request): JsonResponse
    {
        $authorLastName = $request->query->get('author');

        if (empty($authorLastName) || !is_string($authorLastName)) {
            return $this->json(['error' => 'Invalid or missing "author" parameter'], 400);
        }

        $books = $bookRepository->findByAuthorLastName($authorLastName);
        return $this->json($books);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(BookRepository $bookRepository, int $id): JsonResponse
    {
        $book = $bookRepository->find($id);
        if (!$book) {
            return $this->json(['error' => 'Book not found'], 404);
        }
        return $this->json($book);
    }



    #[Route('/{id}', methods: ['PUT'])]
    public function update(Request $request, BookRepository $bookRepository, EntityManagerInterface $em, int $id): JsonResponse
    {
        $book = $bookRepository->find($id);
        if (!$book) {
            return $this->json(['error' => 'Book not found'], 404);
        }

        $title = $request->request->get('title');
        $description = $request->request->get('description');
        $publishedAt = $request->request->get('published_at');
        $authorIds = $request->request->all('authors');

        $book->setTitle($title);
        $book->setDescription(  $description ?? null);
        $book->setPublishedAt(new \DateTime($publishedAt));

        // Authors
        $authors = $em->getRepository(Author::class)->findBy(['id' => $authorIds]);

        foreach ($authors as $author) {
            $book->addAuthor($author);
        }

        // Image
        /** @var UploadedFile $image */
        $image = $request->files->get('image');
        if ($image) {
            $fileName = uniqid().'.'.$image->guessExtension();
            try {
                $image->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
                $book->setImage($fileName);
            } catch (FileException $e) {
                return $this->json(['error' => 'Failed to upload image'], 400);
            }
        }

        $em->flush();
        return $this->json($book);
    }
}

