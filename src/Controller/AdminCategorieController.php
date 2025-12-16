<?php

namespace App\Controller;

use App\Entity\CategoriePosts;
use App\Form\CategoriePosts1Type;
use App\Repository\CategoriePostsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/categorie')]
final class AdminCategorieController extends AbstractController
{
    
    #[Route(name: 'app_admin_categorie_index', methods: ['GET'])]
    public function index(CategoriePostsRepository $categoriePostsRepository): Response
    {
        return $this->render('admin_categorie/index.html.twig', [
            'categorie_posts' => $categoriePostsRepository->findAll(),
        ]);
    }

    #[Route('/admin/categorie/new', name: 'app_admin_categorie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categoriePost = new CategoriePosts();
        $form = $this->createForm(CategoriePosts1Type::class, $categoriePost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categoriePost);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_categorie/new.html.twig', [
            'categorie_post' => $categoriePost,
            'form' => $form,
        ]);
    }

    #[Route('/admin/categorie/{id}', name: 'app_admin_categorie_show', methods: ['GET'])]
    public function show(CategoriePosts $categoriePost): Response
    {
        return $this->render('admin_categorie/show.html.twig', [
            'categorie_post' => $categoriePost,
        ]);
    }

    #[Route('/admin/categorie/edit/{id}', name: 'app_admin_categorie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CategoriePosts $categoriePost, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoriePosts1Type::class, $categoriePost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_categorie/edit.html.twig', [
            'categorie_post' => $categoriePost,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_categorie_delete', methods: ['POST'])]
    public function delete(Request $request, CategoriePosts $categoriePost, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categoriePost->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($categoriePost);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_categorie_index', [], Response::HTTP_SEE_OTHER);
    }
}
