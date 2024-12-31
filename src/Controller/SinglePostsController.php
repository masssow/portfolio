<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Entity\Message;
use App\Form\MessageFormType;
use App\Repository\PostsRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\CategoriePostsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SinglePostsController extends AbstractController
{
    #[Route('/article/{id}', name: 'app_single_posts', methods: ['GET', 'POST'])]
    public function index(Request $request,PostsRepository $postsRepository, EntityManagerInterface $em,
        int $id, Posts $post, MessageRepository $messageRepository, CategoriePostsRepository $categoriePostsRepository, PaginatorInterface $pagination): Response 
        {

        $comments = $messageRepository->findBy(['post' => $post], ['createdAt' => 'DESC']);

        $categories = $categoriePostsRepository->findAll();

            // Charger le post courant
            $currentPost = $em->getRepository(Posts::class)->find($id);
            if (!$currentPost) {
                throw $this->createNotFoundException('Article introuvable.');
            }

        // Charger les commentaires du post
        $messages = $post->getMessages();

        // Charger le post suivant et précédent
        $nextPost = $postsRepository->findNextPost($currentPost);
        $previusPost = $postsRepository->findPreviusPost($currentPost);

        // Création et traitement du formulaire de commentaire
        $message = new Message();
        $form = $this->createForm(MessageFormType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification du champ honeypot
            $honeypot = $request->get('honeypot_field');
            if (!empty($honeypot)) {
                $this->addFlash('error', 'Spam détecté. Votre commentaire n\'a pas été enregistré.');
                return $this->redirectToRoute('app_single_posts', ['id' => $id]);
            }

            // Associer le message au post et sauvegarder en base
            $message->setPost($post);
            $message->setCreatedAt(new \DateTimeImmutable());

            // dd($form->getData());
            $em->persist($message);
            $em->flush();

            // Confirmation et redirection après succès
            $this->addFlash('success', 'Votre commentaire a été ajouté avec succès.');
            return $this->redirectToRoute('app_single_posts', ['id' => $id]);
        }
        $postsByCategorieRaw = $postsRepository->countPostsByCategorie();

        $postsByCategorie = [];
        foreach ($postsByCategorieRaw as $item) {
            $postsByCategorie[$item['id']] = $item['postCount'];
        }

        $totalComments =  $messageRepository->countByPost($post);
        // dd($totalComments);

        $popularPosts = $postsRepository->findPopularPost(4);

        $totalPost = $categoriePostsRepository->count();

        // dd($comments);
        return $this->render('single_posts/index.html.twig', [
            'nextPost'      => $nextPost,
            'previusPost'   => $previusPost,
            'currentPost'   => $currentPost,
            'messages'      => $messages,
            'form'          => $form->createView(),
            'post'          => $post,
            'totalComments' => $totalComments,
            'totalPost'      => $totalPost,
            'categories'     => $categories,
            'postsByCategorie' => $postsByCategorie,
            'popularPosts'    => $popularPosts,

        ]);
    }


    }
        
