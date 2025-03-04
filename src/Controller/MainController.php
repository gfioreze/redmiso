<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class MainController extends AbstractController
{
    public function __construct(ArticleRepository $articleRepository, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager)
    {
        $this->articleRepository = $articleRepository;
        $this->categoryRepository = $categoryRepository;
        $this->entityManager = $entityManager;
    }

    private function getCategories(): array
    {
        return $this->categoryRepository->findAll();
    }

    #[Route('/', name: 'app_main', methods: ['GET'])]
    //#[IsGranted(User::ROLE_ADMIN)]
    public function index(): Response
    {
        $articles = $this->articleRepository->findBy([], null, 3);
        $categories = $this->getCategories();

        //$this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render('main/main.html.twig', [
            'articles' => $articles,
            'categories' => $categories
        ]);
    }

    #[Route('/article/{slug}', name: 'show_article', requirements: ['slug' => Requirement::ASCII_SLUG], methods: ['GET'])]
    public function showArticle(string $slug): Response
    {
        $article = $this->articleRepository->findOneBy(['slug' => $slug]);
        $categories = $this->getCategories();

        if (!$article) {
            throw $this->createNotFoundException('The article does not exist');
        }

        return $this->render('article/article_show.html.twig', [
            'article' => $article,
            'categories' => $categories
        ]);
    }

    #[Route('/search', name: 'articles_search', methods: ['GET'])]
    public function search(Request $request, ArticleRepository $articleRepository): Response
    {
        $categories = $this->getCategories();
        $query = (string) $request->query->get('q', '');
        $articles = $articleRepository->findBySearchQuery($query);

        return $this->render('main/search.html.twig', [
           'articles' => $articles,
            'query' => $query,
            'categories' => $categories
        ]);
    }
}