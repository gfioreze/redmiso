<?php

namespace App\Tests\Util;

use App\Form\CommentType;
use App\Service\ArticleService;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * A test double that mimics MainController behavior for testing
 */
class TestMainController
{
    private ArticleService $articleService;
    private TemplateRenderer $templateRenderer;
    private FormInterface $form;
    
    public function __construct(
        ArticleService $articleService,
        TemplateRenderer $templateRenderer,
        FormInterface $form
    ) {
        $this->articleService = $articleService;
        $this->templateRenderer = $templateRenderer;
        $this->form = $form;
    }
    
    /**
     * Renders a template
     * 
     * @param string $view The template name
     * @param array $parameters The template parameters
     * @param Response|null $response The response object
     * @return Response The response
     */
    protected function render(string $view, array $parameters = [], ?Response $response = null): Response
    {
        return $this->templateRenderer->render($view, $parameters, $response);
    }
    
    /**
     * Creates a form
     * 
     * @param string $type The form type
     * @param mixed|null $data The initial data
     * @param array $options The form options
     * @return FormInterface The form
     */
    public function createForm(string $type, $data = null, array $options = []): FormInterface
    {
        return $this->form;
    }
    
    public function index(): Response
    {
        $data = $this->articleService->getHomePageData();
        return $this->render('main/main.html.twig', $data);
    }
    
    public function getArticlesByCategory(string $categoryName): Response
    {
        try {
            $data = $this->articleService->getArticlesByCategory($categoryName);
            return $this->render('category/get_by_category.html.twig', $data);
        } catch (\Exception $e) {
            throw new \Exception('Category not found');
        }
    }
    
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
            throw new \Exception($e->getMessage());
        }
    }
    
    public function search(Request $request): Response
    {
        $query = (string)$request->query->get('q', '');
        $data = $this->articleService->searchArticles($query);
        
        return $this->render('main/search.html.twig', $data);
    }
}