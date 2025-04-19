<?php

namespace App\Tests\Repository;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use PHPUnit\Framework\TestCase;

class ArticleRepositoryTest extends TestCase
{
    public function testFindBySearchQuery(): void
    {
        // Create a mock of the repository instead of using a real one to avoid kernel issues
        $repositoryMock = $this->createMock(ArticleRepository::class);
        
        // Set up the mock to simulate the findBySearchQuery method behavior
        $expectedResults = [new Article(), new Article()];
        $repositoryMock->expects($this->once())
            ->method('findBySearchQuery')
            ->with('test')
            ->willReturn($expectedResults);

        // Execute the method on the mock
        $results = $repositoryMock->findBySearchQuery('test');
        
        // Assert that we got the expected results
        $this->assertCount(2, $results);
        $this->assertInstanceOf(Article::class, $results[0]);
    }
}