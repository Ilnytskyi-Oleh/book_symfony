<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/authors')]
class AuthorController extends AbstractController
{

    #[Route('', methods: ['GET'])]
    public function index(AuthorRepository $authorRepository, Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $authors = $authorRepository->findPaginated($page, $limit);
        return $this->json($authors);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {
        $author = new Author();
        $author->setFirstName($request->request->get('first_name'));
        $author->setLastName($request->request->get('last_name'));
        $author->setMiddleName($request->request->get('middle_name'));

        $errors = $validator->validate($author);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], 400);
        }

        $em->persist($author);
        $em->flush();

        return $this->json($author, 201);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(AuthorRepository $authorRepository, int $id): JsonResponse
    {
        $author = $authorRepository->find($id);
        if (!$author) {
            return $this->json(['error' => 'Author not found'], 404);
        }
        return $this->json($author);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(
        int $id,
        Request $request,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        AuthorRepository $authorRepository
    ): JsonResponse
    {
        $author = $authorRepository->find($id);
        if (!$author) {
            return $this->json(['error' => 'Author not found'], 404);
        }

        $author->setFirstName($request->request->get('first_name'));
        $author->setLastName($request->request->get('last_name'));
        $author->setMiddleName($request->request->get('middle_name'));

        $errors = $validator->validate($author);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], 400);
        }

        $errors = $validator->validate($author);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], 400);
        }

        $em->flush();
        return $this->json($author);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em, AuthorRepository $authorRepository): JsonResponse
    {
        $author = $authorRepository->find($id);
        if (!$author) {
            return $this->json(['error' => 'Author not found'], 404);
        }

        $em->remove($author);
        $em->flush();

        return $this->json(null, 204);
    }
}

