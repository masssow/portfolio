<?php

namespace App\Controller;

use App\Repository\PostsRepository;
use App\Repository\CategoriePostsRepository;
use App\Repository\MessageRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostsController extends AbstractController
{
    #[Route('/blog', name: 'app_posts')]
    public function index(
        Request $request,
        PostsRepository $postsRepository,
        CategoriePostsRepository $categoriePostsRepository,
        MessageRepository $messageRepository,
        PaginatorInterface $paginator
    ): Response {
        // Récupérer toutes les catégories
        $categories = $categoriePostsRepository->findAll();

        // Récupérer l'ID de la catégorie sélectionnée
        $categorieId = $request->query->get('categorie_id');
        $categorieId = $categorieId ? (int)$categorieId : null;

        // Déterminer la requête pour les posts (par catégorie ou tous)
        if ($categorieId) {
            $query = $postsRepository->findByCategorie($categorieId);
        } else {
            $query = $postsRepository->findWithPaginator();
        }

        // Paginer les résultats
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );

        // Si la requête est AJAX, retourner uniquement la pagination
        if ($request->isXmlHttpRequest()) {
            return $this->render('posts/_pagination.html.twig', [
                'pagination' => $pagination,
            ]);
        }

        // Récupérer les totaux
        $totalPost = $postsRepository->count([]);
        // $totalComments =  $messageRepository->countByPost($post);
        $postsByCategorieRaw = $postsRepository->countPostsByCategorie();

        $popularPosts = $postsRepository->findPopularPost(4);

        $postsByCategorie = [];
        foreach ($postsByCategorieRaw as $item) {
            $postsByCategorie[$item['id']] = $item['postCount'];
        }
        // dd($postsByCategorie);

        // Retourner la vue complète
        return $this->render('posts/index.html.twig', [
            'categories'    => $categories,
            'pagination'    => $pagination,
            'totalPost'     => $totalPost,
            // 'totalCommens'  => $totalComments,
            'postsByCategorie' => $postsByCategorie,
            'popularPosts'    => $popularPosts,

        ]);
    }


    #[Route('blog/categorie/{id}/', name: 'app_posts_by_categorie', methods: ['GET'])]
    public function PostsByCategorie(
        int $id,
        Request $request,
        PostsRepository $postsRepository,
        CategoriePostsRepository $categoriePostsRepository,
        PaginatorInterface $paginator, MessageRepository $messageRepository
    ): Response {
        // Vérifier si la catégorie existe
        $categorie = $categoriePostsRepository->find($id);
        if (!$categorie) {
            throw $this->createNotFoundException('Catégorie introuvable.');
        }

        // Récupérer les posts liés à cette catégorie
        $query = $postsRepository->findByCategorie($id);

        // Paginer les résultats
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5 // Nombre d'articles par page
        );

        // Charger toutes les catégories pour l'aside
        $categories = $categoriePostsRepository->findAll();

        $totalPosts = count([$query]);
        // $totalComments =  $messageRepository->countByPost($post);

        $totalComments = $categoriePostsRepository->count([]);

        // Renvoyer la vue
        return $this->render('posts/categorie.html.twig', [
            'categorie'    => $categorie,
            'pagination'   => $pagination,
            'categories'   => $categories,
            'totalPosts'   => $totalPosts,
            'totalComments' => $totalComments,
        ]);
    }

}
