<?php

namespace App\Controller;

use App\Entity\CategoriePosts;
use App\Repository\PostsRepository;
use App\Repository\CategoriePostsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostsController extends AbstractController
{
    #[Route('/blog', name: 'app_posts')]
    public function index(Request $request, PostsRepository $postsRepository, CategoriePostsRepository $categoriePostsRepository, PaginatorInterface $paginator): Response
    {

        $categorie = $categoriePostsRepository->findBy([], null, 3 );
        $query = $postsRepository->findWithPaginator();

        $pagination = $paginator->paginate( $query, $request->query->getInt('page', 1),5  );

        // $post = $postsRepository->findAll();

        return $this->render('posts/index.html.twig', [
            // 'posts'         => $post,
            'categories'    => $categorie,
            'pagination'    => $pagination   
        ]);
    }
}
