<?php

namespace App\Tests\Entity;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class ArticleTest extends TestCase
{
    private Article $article;

    protected function setUp(): void
    {
        $this->article = new Article();
    }

    public function testConstructor(): void
    {
        // Test that comments collection is initialized
        $this->assertCount(0, $this->article->getComments());
    }

    public function testGetterAndSetterForTitle(): void
    {
        $this->assertNull($this->article->getTitle());
        
        $title = 'Test Article Title';
        $this->article->setTitle($title);
        
        $this->assertSame($title, $this->article->getTitle());
    }

    public function testGetterAndSetterForContent(): void
    {
        $this->assertNull($this->article->getContent());
        
        $content = 'This is a test content for the article.';
        $this->article->setContent($content);
        
        $this->assertSame($content, $this->article->getContent());
    }

    public function testGetterAndSetterForSlug(): void
    {
        $this->assertNull($this->article->getSlug());
        
        $slug = 'test-article-slug';
        $this->article->setSlug($slug);
        
        $this->assertSame($slug, $this->article->getSlug());
    }

    public function testGetterAndSetterForImage(): void
    {
        $this->assertNull($this->article->getImage());
        
        $image = 'test-image.jpg';
        $this->article->setImage($image);
        
        $this->assertSame($image, $this->article->getImage());
    }

    public function testGetterAndSetterForCategory(): void
    {
        $this->assertNull($this->article->getCategory());
        
        $category = $this->createMock(Category::class);
        $this->article->setCategory($category);
        
        $this->assertSame($category, $this->article->getCategory());
    }

    public function testGetterAndSetterForCreatedBy(): void
    {
        $this->assertNull($this->article->getCreatedBy());
        
        $user = $this->createMock(User::class);
        $this->article->setCreatedBy($user);
        
        $this->assertSame($user, $this->article->getCreatedBy());
    }

    public function testAddComment(): void
    {
        $comment = $this->createMock(Comment::class);
        
        // Expect setArticle to be called on the comment
        $comment->expects($this->once())
            ->method('setArticle')
            ->with($this->article);
            
        // Add the comment
        $this->article->addComment($comment);
        
        // Verify comment was added
        $this->assertCount(1, $this->article->getComments());
        $this->assertTrue($this->article->getComments()->contains($comment));
    }

    public function testRemoveComment(): void
    {
        $comment = $this->createMock(Comment::class);
        
        // Setup: First add the comment
        $comment->expects($this->atLeastOnce())
            ->method('setArticle')
            ->withAnyParameters();
        $this->article->addComment($comment);
        
        // When removing, if the comment article is this article, set it to null
        $comment->expects($this->once())
            ->method('getArticle')
            ->willReturn($this->article);
            
        // Remove the comment
        $this->article->removeComment($comment);
        
        // Verify comment was removed
        $this->assertCount(0, $this->article->getComments());
        $this->assertFalse($this->article->getComments()->contains($comment));
    }
}