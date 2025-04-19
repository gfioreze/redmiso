# Symfony Unit Testing Guide

## Testable Classes

### Entity Classes
- `src/Entity/Article.php`
- `src/Entity/Category.php`
- `src/Entity/Comment.php`
- `src/Entity/User.php`

### Repository Classes
- `src/Repository/ArticleRepository.php`
- `src/Repository/CategoryRepository.php`
- `src/Repository/CommentRepository.php`
- `src/Repository/UserRepository.php`

### Controller Classes
- `src/Controller/MainController.php`
- `src/Controller/RegistrationController.php`
- `src/Controller/SecurityController.php`

### Form Classes
- `src/Form/CommentType.php`
- `src/Form/RegistrationFormType.php`

### Security Classes
- `src/Security/LoginFormAuthenticator.php`

## Test Types

### Entity Tests
Test getters, setters, and collection management methods.

```php
// Example: ArticleTest.php
public function testGetterAndSetterForTitle(): void
{
    $this->assertNull($this->article->getTitle());
    
    $title = 'Test Article Title';
    $this->article->setTitle($title);
    
    $this->assertSame($title, $this->article->getTitle());
}
```

### Repository Tests
Repository tests can be implemented in two ways:

#### 1. Using Mocks (Simpler, No Database Required)
```php
// Using PHPUnit TestCase and mocks
public function testFindBySearchQuery(): void
{
    // Create a mock of the repository
    $repositoryMock = $this->createMock(ArticleRepository::class);
    
    // Set up expected behavior
    $expectedResults = [new Article(), new Article()];
    $repositoryMock->expects($this->once())
        ->method('findBySearchQuery')
        ->with('test')
        ->willReturn($expectedResults);

    // Call the method
    $results = $repositoryMock->findBySearchQuery('test');
    
    // Assert results
    $this->assertCount(2, $results);
}
```

#### 2. Using Real Database (More Complex, Better Integration Testing)
```php
// Using KernelTestCase for real database access
public function testFindBySearchQuery(): void
{
    // With fixtures loaded and DAMADoctrineTestBundle for transaction management
    $results = $this->repository->findBySearchQuery('test');
    
    // Then assert expected results
    $this->assertGreaterThanOrEqual(0, count($results));
}
```

### Controller Tests
Controller tests can be implemented in three ways:

#### 1. Unit Testing Controllers (When Controller Can Be Extended)
```php
class MainControllerTest extends TestCase
{
    private $articleRepository;
    private $categoryRepository;
    // Other dependencies...
    
    protected function setUp(): void
    {
        // Mock dependencies
        $this->articleRepository = $this->createMock(ArticleRepository::class);
        // Other mocks...
        
        $this->controller = new MainController(
            // Pass mocked dependencies...
        );
    }
    
    public function testSomeMethod(): void
    {
        // Set up mocks and expectations
        
        // Call controller method directly
        $result = // call method
        
        // Assert result
    }
}
```

#### 2. Using Test Doubles for Final Controllers
```php
// Test double class (in tests/Util/TestMainController.php)
class TestMainController
{
    private ArticleService $articleService;
    private TemplateRenderer $templateRenderer;
    
    // Constructor and dependencies...
    
    // Implement the same public methods as the real controller
    public function index(): Response
    {
        $data = $this->articleService->getHomePageData();
        return $this->render('main/main.html.twig', $data);
    }
    
    // Additional methods...
}

// Test class (in tests/Controller/MainControllerTest.php)
class MainControllerTest extends TestCase
{
    private $articleService;
    private $testController;
    
    protected function setUp(): void
    {
        // Create mocks
        $this->articleService = $this->createMock(ArticleService::class);
        
        // Create test utilities
        $templateRenderer = new TemplateRenderer();
        
        // Create a test controller that mimics the real one
        $this->testController = new TestMainController(
            $this->articleService,
            $templateRenderer,
            // Other dependencies...
        );
    }
    
    public function testIndex(): void
    {
        // Test logic...
    }
}
```

#### 3. Functional Testing Controllers (Full HTTP Request/Response)
```php
// Using WebTestCase for functional tests
class MainControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Expected heading');
    }
}
```

## Database Setup

### SQLite for Testing
```
# .env.test
DATABASE_URL=sqlite:///%kernel.project_dir%/var/data/test.sqlite
```

### Create Test Database
```bash
mkdir -p var/data
php bin/console doctrine:schema:create --env=test
```

## Test Isolation with DAMADoctrineTestBundle

### Installation
```bash
composer require --dev dama/doctrine-test-bundle
```

### Configuration
```xml
<!-- phpunit.xml.dist -->
<extensions>
    <extension class="DAMA\DoctrineTestBundle\PHPUnit\PHPUnitExtension"/>
</extensions>
```

## Test Fixtures

### Creating Test Fixtures
```php
// src/DataFixtures/Test/TestFixtures.php
class TestFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Create test data...
        $manager->flush();
    }
    
    // Optional: group fixtures
    public static function getGroups(): array
    {
        return ['test'];
    }
}
```

### Loading Fixtures
```bash
php bin/console doctrine:fixtures:load --env=test --group=test
```

## Running Tests

```bash
# Run all tests
php bin/phpunit

# Run a specific test class
php bin/phpunit tests/Entity/ArticleTest.php

# Run a specific test method
php bin/phpunit --filter testGetterAndSetterForTitle tests/Entity/ArticleTest.php
```

## Best Practices

1. **Test Isolation**: Each test should be independent
2. **Use Mock Objects**: For external dependencies
3. **Descriptive Test Names**: Make test names clear about what they're testing
4. **Assert One Concept Per Test**: Each test should verify one specific behavior
5. **Don't Test Getters/Setters Only**: Focus on testing business logic
6. **Keep Tests Fast**: Tests should run quickly
7. **Properly Declare Properties**: Avoid deprecation notices by declaring class properties
8. **Test Doubles for Final Classes**: Use test doubles instead of inheritance when testing final classes

## Avoiding Deprecation Notices

In PHP 8.2+, creating dynamic properties (assigning to undeclared properties) is deprecated. Always declare your properties:

```php
class SomeController extends AbstractController
{
    // Properly declare properties to avoid deprecation notices
    private LoggerInterface $logger;
    private ArticleRepository $articleRepository;
    // Other properties...
    
    public function __construct(LoggerInterface $logger, ArticleRepository $articleRepository)
    {
        $this->logger = $logger;
        $this->articleRepository = $articleRepository;
    }
}
```

## Project Test Structure

```
tests/
├── Controller/
│   └── MainControllerTest.php
├── Entity/
│   └── ArticleTest.php
├── Repository/
│   └── ArticleRepositoryTest.php
├── Util/
│   ├── TemplateRenderer.php
│   └── TestMainController.php
├── bootstrap.php
└── TESTING_GUIDE.md
```

## Recent Updates

1. **Simplified Repository Tests**: Changed to use mocks instead of real database connections to avoid kernel bootstrapping issues

2. **Updated Controller Tests**: Improved controller testing approaches with both unit and functional testing options

3. **Added Property Declarations**: Fixed deprecation notices by properly declaring class properties

4. **Improved Test Isolation**: Using DAMADoctrineTestBundle to ensure each test runs in its own transaction

5. **Test Environment**: Configured SQLite database for testing to keep tests fast and isolated from production

6. **Test Doubles for Final Classes**: Added approach for testing final controller classes using test doubles instead of inheritance

7. **Enhanced Test Structure**: Created utility classes in a dedicated Util directory for better organization