<?php

namespace App\Tests\Entity;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    private Comment $comment;

    protected function setUp(): void
    {
        $this->comment = new Comment();
    }

    public function testGetAndSetContent(): void
    {
        $this->assertNull($this->comment->getContent());

        $content = 'This is a test comment.';
        $this->comment->setContent($content);

        $this->assertSame($content, $this->comment->getContent());
    }

    public function testSetCreatedAt(): void
    {
        $this->assertNull($this->comment->getCreatedAt());

        $this->comment->setCreatedAt();

        $this->assertInstanceOf(\DateTimeImmutable::class, $this->comment->getCreatedAt());
    }

    public function testGetAndSetArticle(): void
    {
        $this->assertNull($this->comment->getArticle());

        $article = $this->createMock(Article::class);
        $this->comment->setArticle($article);

        $this->assertSame($article, $this->comment->getArticle());
    }

    public function testGetAndSetCommentedBy(): void
    {
        $this->assertNull($this->comment->getCommentedBy());

        $user = $this->createMock(User::class);
        $this->comment->setCommentedBy($user);

        $this->assertSame($user, $this->comment->getCommentedBy());
    }
}