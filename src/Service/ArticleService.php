<?php

namespace App\Service;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class ArticleService
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private CategoryRepository $categoryRepository,
        private CommentRepository $commentRepository,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger
    ) {}

    /**
     * @return array<int, Category>
     */
    public function getCategories(): array
    {
        return $this->categoryRepository->findAll();
    }

    /**
     * @return array<string, mixed>
     */
    public function getHomePageData(): array
    {
        $articles = $this->articleRepository->findBy([], ['id' => 'DESC']);
        $categories = $this->getCategories();

        return [
            'articles' => $articles,
            'categories' => $categories
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getArticlesByCategory(string $categoryName): array
    {
        $articleCategory = $this->categoryRepository->findOneBy(['name' => $categoryName]);

        if (!$articleCategory) {
            throw new \Exception('Category not found');
        }

        $articles = $this->articleRepository->findBy(['category' => $articleCategory]);
        $categories = $this->getCategories();

        return [
            'articles' => $articles,
            'categories' => $categories
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getArticleData(string $slug): array
    {
        $article = $this->articleRepository->findOneBy(['slug' => $slug]);

        if (!$article) {
            throw new \Exception('The article does not exist');
        }

        $categories = $this->getCategories();
        $comments = $this->commentRepository->findBy(['article' => $article]);

        return [
            'article' => $article,
            'categories' => $categories,
            'comments' => $comments,
            'slug' => $slug
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function searchArticles(string $query): array
    {
        $articles = $this->articleRepository->findBySearchQuery($query);
        $categories = $this->getCategories();

        return [
            'articles' => $articles,
            'query' => $query,
            'categories' => $categories
        ];
    }

    /**
     * Creates a new comment for an article
     * 
     * @return Article|null The article with the new comment, or null if there was an error
     */
    public function createComment(string $slug, User $user, Comment $comment): ?Article
    {
        $article = $this->articleRepository->findOneBy(['slug' => $slug]);

        if (!$article) {
            throw new \Exception('Article not found.');
        }

        if (!$article->getTitle()) {
            throw new \Exception('Article title is missing.');
        }

        $comment->setCommentedBy($user);
        $article->addComment($comment);

        try {
            $this->entityManager->persist($comment);
            $this->entityManager->flush();
            return $article;
        } catch (\Exception $e) {
            $this->logger->error("Error saving comment: " . $e->getMessage());
            return null;
        }
    }
}