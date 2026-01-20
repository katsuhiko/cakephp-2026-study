<?php
declare(strict_types=1);

namespace CmsCore\Test\Application\UseCase;

use CmsCore\Application\UseCase\CreateArticleUseCase;
use CmsCore\Domain\Model\Article;
use CmsCore\Domain\Repository\ArticleRepositoryInterface;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * CreateArticleUseCase Test Case
 */
class CreateArticleUseCaseTest extends TestCase
{
    /**
     * Test execute with valid data
     *
     * @return void
     */
    public function testExecuteWithValidData(): void
    {
        // Arrange
        $input = [
            'user_id' => 1,
            'title' => 'Test Article',
            'slug' => 'test-article',
            'body' => 'This is a test article body.',
            'published' => true,
            'tag_ids' => [1, 2, 3],
        ];

        $savedArticle = Article::reconstruct([
            'id' => 1,
            'user_id' => 1,
            'title' => 'Test Article',
            'slug' => 'test-article',
            'body' => 'This is a test article body.',
            'published' => true,
            'tag_ids' => [1, 2, 3],
            'created' => '2026-01-20 10:00:00',
            'modified' => '2026-01-20 10:00:00',
        ]);

        $articleRepository = $this->createMock(ArticleRepositoryInterface::class);
        $articleRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function ($article) use ($input) {
                return $article instanceof Article &&
                    $article->userId === $input['user_id'] &&
                    $article->title === $input['title'] &&
                    $article->slug === $input['slug'] &&
                    $article->body === $input['body'] &&
                    $article->published === $input['published'] &&
                    $article->tagIds === $input['tag_ids'];
            }))
            ->willReturn($savedArticle);

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('info')
            ->with(
                'Article created successfully',
                [
                    'article_id' => 1,
                    'user_id' => 1,
                ],
            );

        $useCase = new CreateArticleUseCase($articleRepository, $logger);

        // Act
        $result = $useCase->execute($input);

        // Assert
        $this->assertTrue($result['success'], 'UseCase execution should be successful');
        $this->assertSame(1, $result['articleId']);
        $this->assertSame([], $result['errors']);
    }

    /**
     * Test execute with minimal data
     *
     * @return void
     */
    public function testExecuteWithMinimalData(): void
    {
        // Arrange
        $input = [
            'user_id' => 1,
            'title' => 'Minimal',
            'slug' => 'minimal',
            'body' => 'Body',
            'published' => false,
            'tag_ids' => [],
        ];

        $savedArticle = Article::reconstruct([
            'id' => 2,
            'user_id' => 1,
            'title' => 'Minimal',
            'slug' => 'minimal',
            'body' => 'Body',
            'published' => false,
            'tag_ids' => [],
            'created' => '2026-01-20 10:00:00',
            'modified' => '2026-01-20 10:00:00',
        ]);

        $articleRepository = $this->createMock(ArticleRepositoryInterface::class);
        $articleRepository
            ->expects($this->once())
            ->method('save')
            ->willReturn($savedArticle);

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('info');

        $useCase = new CreateArticleUseCase($articleRepository, $logger);

        // Act
        $result = $useCase->execute($input);

        // Assert
        $this->assertTrue($result['success'], 'UseCase execution should be successful');
        $this->assertSame(2, $result['articleId']);
        $this->assertSame([], $result['errors']);
    }

    /**
     * Test execute with empty title returns domain validation error
     *
     * @return void
     */
    public function testExecuteWithEmptyTitleReturnsDomainValidationError(): void
    {
        // Arrange
        $input = [
            'user_id' => 1,
            'title' => '',
            'slug' => 'test-article',
            'body' => 'Body',
            'published' => false,
            'tag_ids' => [],
        ];

        $articleRepository = $this->createMock(ArticleRepositoryInterface::class);
        $articleRepository
            ->expects($this->never())
            ->method('save');

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('warning')
            ->with(
                'Article creation failed: domain validation error',
                $this->callback(function (mixed $context) use ($input): bool {
                    return is_array($context) &&
                        $context['error'] === 'Title cannot be empty' &&
                        $context['input'] === $input;
                }),
            );

        $useCase = new CreateArticleUseCase($articleRepository, $logger);

        // Act
        $result = $useCase->execute($input);

        // Assert
        $this->assertFalse($result['success'], 'UseCase execution should fail');
        $this->assertNull($result['articleId'], 'Article ID should be null on failure');
        $this->assertSame(['Title cannot be empty'], $result['errors']);
    }

    /**
     * Test execute with invalid slug returns domain validation error
     *
     * @return void
     */
    public function testExecuteWithInvalidSlugReturnsDomainValidationError(): void
    {
        // Arrange
        $input = [
            'user_id' => 1,
            'title' => 'Test',
            'slug' => 'Invalid Slug',
            'body' => 'Body',
            'published' => false,
            'tag_ids' => [],
        ];

        $articleRepository = $this->createMock(ArticleRepositoryInterface::class);
        $articleRepository
            ->expects($this->never())
            ->method('save');

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('warning');

        $useCase = new CreateArticleUseCase($articleRepository, $logger);

        // Act
        $result = $useCase->execute($input);

        // Assert
        $this->assertFalse($result['success'], 'UseCase execution should fail');
        $this->assertNull($result['articleId'], 'Article ID should be null on failure');
        $this->assertSame(['Slug can only contain lowercase letters, numbers, and hyphens'], $result['errors']);
    }

    /**
     * Test execute with empty body returns domain validation error
     *
     * @return void
     */
    public function testExecuteWithEmptyBodyReturnsDomainValidationError(): void
    {
        // Arrange
        $input = [
            'user_id' => 1,
            'title' => 'Test',
            'slug' => 'test-article',
            'body' => '',
            'published' => false,
            'tag_ids' => [],
        ];

        $articleRepository = $this->createMock(ArticleRepositoryInterface::class);
        $articleRepository
            ->expects($this->never())
            ->method('save');

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('warning');

        $useCase = new CreateArticleUseCase($articleRepository, $logger);

        // Act
        $result = $useCase->execute($input);

        // Assert
        $this->assertFalse($result['success'], 'UseCase execution should fail');
        $this->assertNull($result['articleId'], 'Article ID should be null on failure');
        $this->assertSame(['Body cannot be empty'], $result['errors']);
    }

    /**
     * Test execute with repository exception returns unexpected error
     *
     * @return void
     */
    public function testExecuteWithRepositoryExceptionReturnsUnexpectedError(): void
    {
        // Arrange
        $input = [
            'user_id' => 1,
            'title' => 'Test Article',
            'slug' => 'test-article',
            'body' => 'Body',
            'published' => false,
            'tag_ids' => [],
        ];

        $articleRepository = $this->createMock(ArticleRepositoryInterface::class);
        $articleRepository
            ->expects($this->once())
            ->method('save')
            ->willThrowException(new Exception('Database connection failed'));

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('error')
            ->with(
                'Article creation failed: unexpected error',
                $this->callback(function (mixed $context) use ($input): bool {
                    return is_array($context) &&
                        $context['error'] === 'Database connection failed' &&
                        $context['input'] === $input;
                }),
            );

        $useCase = new CreateArticleUseCase($articleRepository, $logger);

        // Act
        $result = $useCase->execute($input);

        // Assert
        $this->assertFalse($result['success'], 'UseCase execution should fail');
        $this->assertNull($result['articleId'], 'Article ID should be null on failure');
        $this->assertSame(['An unexpected error occurred'], $result['errors']);
    }

    /**
     * Test execute logs success with article ID
     *
     * @return void
     */
    public function testExecuteLogsSuccessWithArticleId(): void
    {
        // Arrange
        $input = [
            'user_id' => 5,
            'title' => 'Log Test',
            'slug' => 'log-test',
            'body' => 'Body',
            'published' => true,
            'tag_ids' => [],
        ];

        $savedArticle = Article::reconstruct([
            'id' => 999,
            'user_id' => 5,
            'title' => 'Log Test',
            'slug' => 'log-test',
            'body' => 'Body',
            'published' => true,
            'tag_ids' => [],
            'created' => '2026-01-20 10:00:00',
            'modified' => '2026-01-20 10:00:00',
        ]);

        $articleRepository = $this->createMock(ArticleRepositoryInterface::class);
        $articleRepository
            ->expects($this->once())
            ->method('save')
            ->willReturn($savedArticle);

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('info')
            ->with(
                'Article created successfully',
                [
                    'article_id' => 999,
                    'user_id' => 5,
                ],
            );

        $useCase = new CreateArticleUseCase($articleRepository, $logger);

        // Act
        $result = $useCase->execute($input);

        // Assert
        $this->assertTrue($result['success'], 'UseCase execution should be successful');
        $this->assertSame(999, $result['articleId']);
    }

    /**
     * Test execute returns result with expected structure
     *
     * @return void
     */
    public function testExecuteReturnsResultWithExpectedStructure(): void
    {
        // Arrange
        $input = [
            'user_id' => 1,
            'title' => 'Structure Test',
            'slug' => 'structure-test',
            'body' => 'Body',
            'published' => false,
            'tag_ids' => [],
        ];

        $savedArticle = Article::reconstruct([
            'id' => 1,
            'user_id' => 1,
            'title' => 'Structure Test',
            'slug' => 'structure-test',
            'body' => 'Body',
            'published' => false,
            'tag_ids' => [],
            'created' => '2026-01-20 10:00:00',
            'modified' => '2026-01-20 10:00:00',
        ]);

        $articleRepository = $this->createMock(ArticleRepositoryInterface::class);
        $articleRepository
            ->expects($this->once())
            ->method('save')
            ->willReturn($savedArticle);

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('info');

        $useCase = new CreateArticleUseCase($articleRepository, $logger);

        // Act
        $result = $useCase->execute($input);

        // Assert
        $this->assertArrayHasKey('success', $result, 'Result should have success key');
        $this->assertArrayHasKey('articleId', $result, 'Result should have articleId key');
        $this->assertArrayHasKey('errors', $result, 'Result should have errors key');
        $this->assertIsBool($result['success'], 'Success should be boolean');
        $this->assertIsArray($result['errors'], 'Errors should be array');
    }
}
