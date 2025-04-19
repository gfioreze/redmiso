<?php

namespace App\DataFixtures\Test;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TestFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Create test user
        $user = new User();
        $user->setEmail("test@example.com");
        $user->setRoles(["ROLE_USER"]);
        $user->setFirstName("Test");
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            "test_password"
        );
        $user->setPassword($hashedPassword);
        $manager->persist($user);

        $user2 = new User();
        $user2->setEmail("test2@example.com");
        $user2->setRoles(["ROLE_ADMIN"]);
        $user2->setFirstName("Test2");
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user2,
            "test_password2"
        );
        $user2->setPassword($hashedPassword);
        $manager->persist($user2);

        // Create test categories
        $category1 = new Category();
        $category1->setName("Test Category");
        $manager->persist($category1);

        $category2 = new Category();
        $category2->setName("Another Category");
        $manager->persist($category2);

        // Create test articles
        $article1 = new Article();
        $article1->setTitle("Test Article 1");
        $article1->setContent("This is test content for article 1");
        $article1->setSlug("test-article-1");
        $article1->setImage("test-image-1.jpg");
        $article1->setCategory($category1);
        $article1->setCreatedBy($user2);
        $manager->persist($article1);

        $article2 = new Article();
        $article2->setTitle("Another Test Article");
        $article2->setContent(
            "This article contains test content for searching"
        );
        $article2->setSlug("another-test-article");
        $article2->setImage("test-image-2.jpg");
        $article2->setCategory($category2);
        $article2->setCreatedBy($user2);
        $manager->persist($article2);

        $manager->flush();

        // Store references to use in tests
        $this->addReference("test-user", $user);
        $this->addReference("test-user2", $user2);
        $this->addReference("test-category-1", $category1);
        $this->addReference("test-category-2", $category2);
        $this->addReference("test-article-1", $article1);
        $this->addReference("test-article-2", $article2);
    }
}
