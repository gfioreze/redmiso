<?php

namespace App\Tests\Controller;

use App\Service\ArticleService;
use App\Tests\Util\TemplateRenderer;
use App\Tests\Util\TestMainController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MainControllerTest extends TestCase
{
    private $articleService;
    private $formView;
    private $templateRenderer;
    private $testController;

    protected function setUp(): void
    {
        $this->articleService = $this->createMock(ArticleService::class);
        $this->formView = $this->createMock(FormView::class);
        
        // Create a form mock that will return our form view
        $form = $this->createMock(FormInterface::class);
        $form->method('createView')->willReturn($this->formView);
        
        // Create a template renderer to capture render calls
        $this->templateRenderer = new TemplateRenderer();
        
        // Create a test controller double that mimics MainController
        $this->testController = new TestMainController(
            $this->articleService,
            $this->templateRenderer,
            $form
        );
    }

    public function testIndexReturnsCorrectArticlesAndCategories(): void
    {
        // Test data
        $testData = [
            'articles' => ['article1', 'article2'],
            'categories' => ['category1', 'category2']
        ];
        
        // Mock service method
        $this->articleService->expects($this->once())
            ->method('getHomePageData')
            ->willReturn($testData);
        
        // Set up expected response
        $expectedResponse = new Response();
        $this->templateRenderer->setRenderReturnValue($expectedResponse);
        
        // Call the controller method
        $response = $this->testController->index();
        
        // Assert response
        $this->assertSame($expectedResponse, $response);
        $this->assertEquals('main/main.html.twig', $this->templateRenderer->getRenderTemplate());
        $this->assertEquals($testData, $this->templateRenderer->getRenderParams());
    }

    public function testGetArticlesByCategoryReturnsCorrectData(): void
    {
        // Test data
        $categoryName = 'Test Category';
        $testData = [
            'articles' => ['article1', 'article2'],
            'categories' => ['category1', 'category2']
        ];
        
        // Mock service method
        $this->articleService->expects($this->once())
            ->method('getArticlesByCategory')
            ->with($categoryName)
            ->willReturn($testData);
        
        // Set up expected response
        $expectedResponse = new Response();
        $this->templateRenderer->setRenderReturnValue($expectedResponse);
        
        // Call the controller method
        $response = $this->testController->getArticlesByCategory($categoryName);
        
        // Assert response
        $this->assertSame($expectedResponse, $response);
        $this->assertEquals('category/get_by_category.html.twig', $this->templateRenderer->getRenderTemplate());
        $this->assertEquals($testData, $this->templateRenderer->getRenderParams());
    }

    public function testShowArticleReturnsCorrectData(): void
    {
        // Test data
        $slug = 'test-article';
        $testData = [
            'article' => 'test article object',
            'categories' => ['category1', 'category2'],
            'comments' => ['comment1', 'comment2'],
            'slug' => $slug
        ];
        
        // Mock service method
        $this->articleService->expects($this->once())
            ->method('getArticleData')
            ->with($slug)
            ->willReturn($testData);
        
        // Set up expected response
        $expectedResponse = new Response();
        $this->templateRenderer->setRenderReturnValue($expectedResponse);
        
        // Call the controller method
        $response = $this->testController->showArticle($slug);
        
        // Assert response
        $this->assertSame($expectedResponse, $response);
        $this->assertEquals('article/article_show.html.twig', $this->templateRenderer->getRenderTemplate());
        
        // Check that all expected keys are in the render params
        $params = $this->templateRenderer->getRenderParams();
        $this->assertArrayHasKey('article', $params);
        $this->assertArrayHasKey('categories', $params);
        $this->assertArrayHasKey('comments', $params);
        $this->assertArrayHasKey('slug', $params);
        $this->assertArrayHasKey('commentForm', $params);
    }

    public function testSearchMethodUsesArticleService(): void
    {
        // Test data
        $testData = [
            'articles' => ['article1', 'article2'],
            'categories' => ['category1', 'category2'],
            'query' => 'test query'
        ];
        
        // Create a Request mock with a search query
        $request = Request::create('/search', 'GET', ['q' => 'test query']);
        
        // Mock service method
        $this->articleService->expects($this->once())
            ->method('searchArticles')
            ->with('test query')
            ->willReturn($testData);
        
        // Set up expected response
        $expectedResponse = new Response();
        $this->templateRenderer->setRenderReturnValue($expectedResponse);
        
        // Call the controller method
        $response = $this->testController->search($request);
        
        // Assert response
        $this->assertSame($expectedResponse, $response);
        $this->assertEquals('main/search.html.twig', $this->templateRenderer->getRenderTemplate());
        $this->assertEquals($testData, $this->templateRenderer->getRenderParams());
    }
}