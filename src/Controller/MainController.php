<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        EntityManagerInterface $entityManager)
    {
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

        $form = $this->createForm(CommentType::class);

        return $this->render('article/article_show.html.twig', [
            'article' => $article,
            'categories' => $categories,
            'commentForm' => $form->createView(),
            'slug' => $slug
        ]);
    }

    #[Route('/comment/{slug}/new', name: 'comment_new', requirements: ['slug' => Requirement::ASCII_SLUG], methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED')]
    public function commentNew(
        Request                $request,
        EntityManagerInterface $entityManager,
        string                 $slug // get the slug directly from the route
    ): Response
    {
        $user = $this->tokenStorage->getToken()?->getUser();
        // Manually fetch the article by slug
        $article = $this->articleRepository->findOneBy(['slug' => $slug]);

        // Check if the article exists
        if (!$article) {
            throw $this->createNotFoundException('Article not found.');
        }

        // Check if article has a title
        if (!$article->getTitle()) {
            throw $this->createNotFoundException('Article title is missing.');
        }

        // Create a new comment and set the user
        $comment = new Comment();
        $comment->setCommentedBy($user);

        // Associate the comment with the article
        $article->addComment($comment);

        // Create the comment form
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //dd($comment);
            //dd($entityManager->getUnitOfWork()->getIdentityMap());
            //dd($user);

            try {
                $entityManager->persist($comment);  // Persist the comment
                $entityManager->persist($article);  // Ensure article is also persisted (if needed)
                $entityManager->flush();  // Commit all changes to the database
            } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
                dd("Unique constraint violation: " . $e->getMessage());
            } catch (\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException $e) {
                dd("Foreign key constraint violation: " . $e->getMessage());
            } catch (\Exception $e) {
                dd("General error: " . $e->getMessage());
            }

            // Redirect to the article page after the comment is added
            return $this->redirectToRoute('show_article', ['slug' => $article->getSlug()], Response::HTTP_SEE_OTHER);
        }

        // If the form is not valid, render the error page
        return $this->render('main/_comment_form_error.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

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