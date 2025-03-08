<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class MainController extends AbstractController
{
    public function __construct(
        TokenStorageInterface    $tokenStorage,
        ArticleRepository      $articleRepository,
        CategoryRepository     $categoryRepository,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    )
    {
        $this->logger = $logger;
        $this->tokenStorage = $tokenStorage;
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

        $form = $this->createForm(CommentType::class)->createView();

        return $this->render('article/article_show.html.twig', [
            'article' => $article,
            'categories' => $categories,
            'commentForm' => $form,
            'slug' => $slug
        ]);
    }

    #[Route('/comment/{slug}/new', name: 'comment_new', requirements: ['slug' => Requirement::ASCII_SLUG], methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED')]
    public function commentNew(
        Security $security,
        Request                $request,
        EntityManagerInterface $entityManager,
        string                 $slug
    ): Response
    {
        //$user = $this->tokenStorage->getToken()?->getUser();
        $user = $security->getUser();
        $article = $this->articleRepository->findOneBy(['slug' => $slug]);

        if (!$article) {
            throw $this->createNotFoundException('Article not found.');
        }

        if (!$article->getTitle()) {
            throw $this->createNotFoundException('Article title is missing.');
        }

        // Create a new comment and set the user
        $comment = new Comment();
        $comment->setCommentedBy($user);

        // Add the comment with to the article
        $article->addComment($comment);

        // Create the comment form
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        // Error handling / debugging
        if ($form->isSubmitted() && $form->isValid()) {

            try
            {
                // Persist the comment
                $entityManager->persist($comment);
                // Ensure article is also persisted (if needed)
                //$entityManager->persist($article);
                // Commit all changes to the database
                $entityManager->flush();
            }
            catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e)
            {
                try {
                    $entityManager->persist($comment);
                    $entityManager->flush();

                    return $this->redirectToRoute('show_article', ['slug' => $slug], Response::HTTP_SEE_OTHER);
                } catch (\Exception $e) {
                    $this->logger->error("Error saving comment: " . $e->getMessage());
                }
            }
        }

        // If the form is not valid, render the error page
        return $this->render('article/article_show.html.twig', [
            'article' => $article,
            'categories' => $this->getCategories(),
            'slug' => $slug,
            'commentForm' => $form->createView()
        ]);
    }

    // This function's sole purpose is to render the comment form
    public function commentForm(): Response
    {
        return $this->render('main/_comment_form.html.twig');
    }

    #[Route('/search', name: 'articles_search', methods: ['GET'])]
    public function search(Request $request, ArticleRepository $articleRepository): Response
    {
        $categories = $this->getCategories();
        $query = (string)$request->query->get('q', '');
        $articles = $articleRepository->findBySearchQuery($query);

        return $this->render('main/search.html.twig', [
            'articles' => $articles,
            'query' => $query,
            'categories' => $categories
        ]);
    }
}