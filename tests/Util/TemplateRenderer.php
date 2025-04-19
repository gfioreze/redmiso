<?php

namespace App\Tests\Util;

use Symfony\Component\HttpFoundation\Response;

/**
 * Helper class that captures template rendering information for testing
 */
class TemplateRenderer
{
    private $renderParams = null;
    private $renderTemplate = null;
    private $renderReturnValue;
    
    /**
     * Captures template render information
     * 
     * @param string $template The template name
     * @param array $params The template parameters
     * @param Response|null $response Optional response object
     * @return Response The response object
     */
    public function render(string $template, array $params = [], ?Response $response = null): Response
    {
        $this->renderTemplate = $template;
        $this->renderParams = $params;
        
        return $this->renderReturnValue ?: new Response();
    }
    
    public function setRenderReturnValue(Response $response): void
    {
        $this->renderReturnValue = $response;
    }
    
    public function getRenderTemplate(): ?string
    {
        return $this->renderTemplate;
    }
    
    public function getRenderParams(): ?array
    {
        return $this->renderParams;
    }
}