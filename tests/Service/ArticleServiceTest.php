<?php

namespace App\Tests\Service;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Service\ArticleService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ArticleServiceTest extends TestCase
{
    /**
     * @var MockObject&ArticleRepository
     */
    private $articleRepository;
    
    /**
     * @var MockObject&CategoryRepository
     */
    private $categoryRepository;
    
    /**
     * @var MockObject&CommentRepository
     */
    private $commentRepository;
    
    /**
     * @var MockObject&EntityManagerInterface
     */
    private $entityManager;
    
    /**
     * @var MockObject&LoggerInterface
     */
    private $logger;
    
    private ArticleService $service;

    protected function setUp(): void
    {        
        // Create proper mock objects using PHPUnit's built-in mocking system
        $this->articleRepository = $this->getMockBuilder(ArticleRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
            
        $this->categoryRepository = $this->getMockBuilder(CategoryRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
            
        $this->commentRepository = $this->getMockBuilder(CommentRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
            
        $this->entityManager = $this->getMockBuilder(EntityManagerInterface::class)
            ->getMock();
            
        $this->logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();
        
        $this->service = new ArticleService(
            $this->articleRepository,
            $this->categoryRepository,
            $this->commentRepository,
            $this->entityManager,
            $this->logger
        );
    }

    public function testGetHomePageData(): void
    {
        // Test data
        $articles = [new Article(), new Article()];
        $categories = [new Category(), new Category()];
        
        // Set up the mocks with willReturn
        $this->articleRepository->method('findBy')
            ->with([], null, 3)
            ->willReturn($articles);
            
        $this->categoryRepository->method('findAll')
            ->willReturn($categories);
        
        // Call the method
        $result = $this->service->getHomePageData();
        
        // Assert the result contains the expected data
        $this->assertSame($articles, $result['articles']);
        $this->assertSame($categories, $result['categories']);
    }

    public function testGetArticlesByCategory(): void
    {
        // Test data
        $categoryName = 'Test Category';
        $category = new Category();
        $articles = [new Article(), new Article()];
        $categories = [new Category(), new Category()];
        
        // Set up the mocks
        $this->categoryRepository->method('findOneBy')
            ->with(['name' => $categoryName])
            ->willReturn($category);
            
        $this->articleRepository->method('findBy')
            ->with(['category' => $category])
            ->willReturn($articles);
            
        $this->categoryRepository->method('findAll')
            ->willReturn($categories);
        
        // Call the method
        $result = $this->service->getArticlesByCategory($categoryName);
        
        // Assert the result
        $this->assertSame($articles, $result['articles']);
        $this->assertSame($categories, $result['categories']);
    }

    public function testGetArticlesByCategory_throwsExceptionWhenCategoryNotFound(): void
    {
        // Configure mock to return null (category not found)
        $this->categoryRepository->method('findOneBy')
            ->willReturn(null);
        
        // Expect exception
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Category not found');
        
        // Call the method
        $this->service->getArticlesByCategory('Non-existent Category');
    }

    public function testCreateComment(): void
    {
        // Test data
        $slug = 'test-article';
        $article = new Article();
        $article->setTitle('Test Article');
        $article->setSlug($slug);
        
        $user = new User();
        $comment = new Comment();
        
        // Set up mocks
        $this->articleRepository->method('findOneBy')
            ->with(['slug' => $slug])
            ->willReturn($article);
        
        // Call the method
        $result = $this->service->createComment($slug, $user, $comment);
        
        // Assertions
        $this->assertSame($article, $result);
        $this->assertSame($user, $comment->getCommentedBy());
        $this->assertTrue($article->getComments()->contains($comment));
    }
}