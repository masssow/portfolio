<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Form\Posts1Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/posts')]
final class AdminPostsController extends AbstractController
{
    #[Route(name: 'app_admin_posts_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $posts = $entityManager
            ->getRepository(Posts::class)
            ->findAll();

        return $this->render('admin_posts/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/admin/posts/new', name: 'app_admin_posts_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $post = new Posts();
        $form = $this->createForm(Posts1Type::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_posts_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_posts/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/admin/posts/{id}', name: 'app_admin_posts_show', methods: ['GET'])]
    public function show(Posts $post): Response
    {
        return $this->render('admin_posts/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/admin/posts/edit/{id}', name: 'app_admin_posts_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Posts $post, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Posts1Type::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_posts_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_posts/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_posts_delete', methods: ['POST'])]
    public function delete(Request $request, Posts $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_posts_index', [], Response::HTTP_SEE_OTHER);
    }
}
