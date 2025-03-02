<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class MainController extends AbstractController
{
    public function __construct(ArticleRepository $articleRepository, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager) {
        $this->articleRepository = $articleRepository;
        $this->categoryRepository = $categoryRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_main')]
    //#[IsGranted(User::ROLE_ADMIN)]
    public function index(): Response
    {
        $articles = $this->articleRepository->findBy([], null, 3);
        $categories = $this->categoryRepository->findAll();
        //dd($articles);
        //$this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render('main/main.twig', [
            'articles' => $articles,
            'categories' => $categories
        ]);
    }
}