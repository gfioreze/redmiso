<?php

namespace App\Tests\Entity;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    public function testEmail(): void
    {
        $this->assertNull($this->user->getEmail());

        $email = 'test@example.com';
        $this->user->setEmail($email);

        $this->assertSame($email, $this->user->getEmail());
        $this->assertSame($email, $this->user->getUserIdentifier());
    }

    public function testRoles(): void
    {
        $this->user->setRoles(['ROLE_ADMIN']);

        $roles = $this->user->getRoles();

        $this->assertContains('ROLE_ADMIN', $roles);
        $this->assertContains('ROLE_USER', $roles); // always added
        $this->assertCount(2, $roles);
    }

    public function testPassword(): void
    {
        $password = 'securehashedpassword';
        $this->user->setPassword($password);

        $this->assertSame($password, $this->user->getPassword());
    }

    public function testFirstName(): void
    {
        $this->assertNull($this->user->getFirstName());

        $this->user->setFirstName('Alice');

        $this->assertSame('Alice', $this->user->getFirstName());
    }

    public function testAddAndRemoveArticle(): void
    {
        $article = $this->createMock(Article::class);
        $article->expects($this->atLeastOnce())
            ->method('setCreatedBy')
            ->with($this->logicalOr(
                $this->isInstanceOf(User::class),
                $this->equalTo(null)
            ));

        $this->user->addArticle($article);

        $this->assertTrue($this->user->getArticles()->contains($article));

        $article->method('getCreatedBy')->willReturn($this->user);
        $article->expects($this->once())->method('setCreatedBy')->with(null);

        $this->user->removeArticle($article);

        $this->assertFalse($this->user->getArticles()->contains($article));
    }

    public function testAddAndRemoveComment(): void
    {
        $comment = $this->createMock(Comment::class);
        $comment->expects($this->atLeastOnce())
            ->method('setCommentedBy')
            ->with($this->logicalOr(
                $this->isInstanceOf(User::class),
                $this->equalTo(null)
            ));

        $this->user->addComment($comment);

        $this->assertTrue($this->user->getComments()->contains($comment));

        $comment->method('getCommentedBy')->willReturn($this->user);
        $comment->expects($this->once())->method('setCommentedBy')->with(null);

        $this->user->removeComment($comment);

        $this->assertFalse($this->user->getComments()->contains($comment));
    }
}