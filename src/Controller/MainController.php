<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class MainController extends AbstractController
{
    public function __construct(ArticleRepository $articleRepository, EntityManagerInterface $entityManager) {
        $this->articleRepository = $articleRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_main')]
    //#[IsGranted(User::ROLE_ADMIN)]
    public function index(): Response
    {
        $articles = $this->articleRepository->findBy([], null, 3);
        //dd($articles);
        //$this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render('main/main.html.twig', [
            'articles' => $articles
        ]);
    }

    #[Route('/article/{slug}', name: 'show_article', requirements: ['slug' => Requirement::ASCII_SLUG])]
    public function showArticle(string $slug, ArticleRepository $articleRepository): Response
    {
        $article = $articleRepository->findOneBy(['slug' => $slug]);

        if (!$article) {
            throw $this->createNotFoundException('The article does not exist');
        }

        return $this->render('article/article_show.html.twig', [
           'article' => $article
        ]);
    }
}