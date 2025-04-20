<?php

namespace App\Tests\Entity;

use App\Entity\Article;
use App\Entity\Category;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    private Category $category;

    protected function setUp(): void
    {
        $this->category = new Category();
    }

    public function testConstructor(): void
    {
        // Ensure articles collection is initialized
        $this->assertCount(0, $this->category->getArticles());
    }

    public function testGetterAndSetterForName(): void
    {
        $this->assertNull($this->category->getName());

        $name = 'Lifestyle';
        $this->category->setName($name);

        $this->assertSame($name, $this->category->getName());
    }

    public function testAddArticle(): void
    {
        $article = $this->createMock(Article::class);

        $article->expects($this->once())
            ->method('setCategory')
            ->with($this->category);

        $this->category->addArticle($article);

        $this->assertCount(1, $this->category->getArticles());
        $this->assertTrue($this->category->getArticles()->contains($article));
    }

    public function testRemoveArticle(): void
    {
        $article = $this->createMock(Article::class);

        // Add article first
        $article->expects($this->atLeastOnce())
            ->method('setCategory')
            ->withAnyParameters();
        $this->category->addArticle($article);

        // Return this category when getCategory is called
        $article->expects($this->once())
            ->method('getCategory')
            ->willReturn($this->category);

        // Expect setCategory(null) to be called
        $article->expects($this->once())
            ->method('setCategory')
            ->with(null);

        $this->category->removeArticle($article);

        $this->assertCount(0, $this->category->getArticles());
        $this->assertFalse($this->category->getArticles()->contains($article));
    }
}