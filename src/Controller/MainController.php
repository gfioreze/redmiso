<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Service\ArticleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class MainController extends AbstractController
{
    public function __construct(
        private ArticleService $articleService
    ) {}

    #[Route('/', name: 'app_main', methods: ['GET'])]
    public function index(): Response
    {
        $data = $this->articleService->getHomePageData();
        return $this->render('main/main.html.twig', $data);
    }

    #[Route('/articles/category/{categoryName}', name: 'get_by_category', methods: ['GET'])]
    public function getArticlesByCategory(string $categoryName): Response
    {
        try {
            $data = $this->articleService->getArticlesByCategory($categoryName);
            return $this->render('category/get_by_category.html.twig', $data);
        } catch (\Exception $e) {
            throw $this->createNotFoundException('Category not found');
        }
    }

    #[Route('/article/{slug}', name: 'article_show', requirements: ['slug' => Requirement::ASCII_SLUG], methods: ['GET'])]
    public function showArticle(string $slug): Response
    {
        try {
            $data = $this->articleService->getArticleData($slug);
            $form = $this->createForm(CommentType::class)->createView();
            
            return $this->render('article/article_show.html.twig', array_merge(
                $data,
                ['commentForm' => $form]
            ));
        } catch (\Exception $e) {
            throw $this->createNotFoundException($e->getMessage());
        }
    }

    #[Route('/article/comment/{slug}/new', name: 'comment_new', requirements: ['slug' => Requirement::ASCII_SLUG], methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED')]
    public function commentNew(
        Security $security,
        Request $request,
        string $slug
    ): Response
    {
        $user = $security->getUser();
        $comment = new Comment();
        
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article = $this->articleService->createComment($slug, $user, $comment);
            
            if ($article) {
                return $this->redirectToRoute('article_show', [
                    'slug' => $article->getSlug()
                ]);
            }
        }

        // If form not valid or error occurred, rerender the article page
        try {
            $data = $this->articleService->getArticleData($slug);
            return $this->render('article/article_show.html.twig', array_merge(
                $data,
                ['commentForm' => $form->createView()]
            ));
        } catch (\Exception $e) {
            throw $this->createNotFoundException($e->getMessage());
        }
    }

    #[Route('/search', name: 'articles_search', methods: ['GET'])]
    public function search(Request $request): Response
    {
        $query = (string)$request->query->get('q', '');
        $data = $this->articleService->searchArticles($query);
        
        return $this->render('main/search.html.twig', $data);
    }
}