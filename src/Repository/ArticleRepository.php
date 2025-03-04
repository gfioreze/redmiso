<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

        /**
         * @return Article[] Returns an array of Article objects
         */
        public function findBySearchQuery(string $query, int $limit = 10): array
        {
            $queryBuilder = $this->createQueryBuilder('a')
                ->where('a.title LIKE :query')
                ->orWhere('a.content LIKE :query')
                ->setParameter('query', '%'.$query.'%')
                ->orderBy('a.id', 'DESC')
                ->setMaxResults($limit)
                ->getQuery();
            return $queryBuilder->getResult();
        }
}
