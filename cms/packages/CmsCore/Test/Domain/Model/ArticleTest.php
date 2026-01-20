<?php
declare(strict_types=1);

namespace CmsCore\Test\Domain\Model;

use CmsCore\Domain\Model\Article;
use CmsCore\Domain\Model\ArticleId;
use DateTimeImmutable;
use DomainException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Article Test Case
 */
class ArticleTest extends TestCase
{
    /**
     * Test create with valid data
     *
     * @return void
     */
    public function testCreateWithValidData(): void
    {
        // Arrange
        $data = [
            'user_id' => 1,
            'title' => 'Test Article',
            'slug' => 'test-article',
            'body' => 'This is a test article body.',
            'published' => true,
            'tag_ids' => [1, 2, 3],
        ];

        // Act
        $article = Article::create($data);

        // Assert
        $this->assertInstanceOf(Article::class, $article);
        $this->assertNull($article->id, 'New article should not have an ID');
        $this->assertSame(1, $article->userId);
        $this->assertSame('Test Article', $article->title);
        $this->assertSame('test-article', $article->slug);
        $this->assertSame('This is a test article body.', $article->body);
        $this->assertTrue($article->published, 'Article should be published');
        $this->assertSame([1, 2, 3], $article->tagIds);
        $this->assertNull($article->created, 'New article should not have created timestamp');
        $this->assertNull($article->modified, 'New article should not have modified timestamp');
    }

    /**
     * Test create with minimal data
     *
     * @return void
     */
    public function testCreateWithMinimalData(): void
    {
        // Arrange
        $data = [
            'user_id' => 1,
            'title' => 'Minimal',
            'slug' => 'minimal',
            'body' => 'Body',
            'published' => false,
            'tag_ids' => [],
        ];

        // Act
        $article = Article::create($data);

        // Assert
        $this->assertSame(0, count($article->tagIds));
        $this->assertFalse($article->published, 'Article should not be published');
    }

    /**
     * Test create with empty title throws exception
     *
     * @return void
     */
    public function testCreateWithEmptyTitleThrowsException(): void
    {
        // Arrange
        $data = [
            'user_id' => 1,
            'title' => '',
            'slug' => 'test-article',
            'body' => 'Body',
            'published' => false,
            'tag_ids' => [],
        ];

        // Assert
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Title cannot be empty');

        // Act
        Article::create($data);
    }

    /**
     * Test create with whitespace-only title throws exception
     *
     * @return void
     */
    public function testCreateWithWhitespaceOnlyTitleThrowsException(): void
    {
        // Arrange
        $data = [
            'user_id' => 1,
            'title' => '   ',
            'slug' => 'test-article',
            'body' => 'Body',
            'published' => false,
            'tag_ids' => [],
        ];

        // Assert
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Title cannot be empty');

        // Act
        Article::create($data);
    }

    /**
     * Test create with too long title throws exception
     *
     * @return void
     */
    public function testCreateWithTooLongTitleThrowsException(): void
    {
        // Arrange
        $data = [
            'user_id' => 1,
            'title' => str_repeat('a', 256),
            'slug' => 'test-article',
            'body' => 'Body',
            'published' => false,
            'tag_ids' => [],
        ];

        // Assert
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Title cannot exceed 255 characters');

        // Act
        Article::create($data);
    }

    /**
     * Test create with empty slug throws exception
     *
     * @return void
     */
    public function testCreateWithEmptySlugThrowsException(): void
    {
        // Arrange
        $data = [
            'user_id' => 1,
            'title' => 'Test',
            'slug' => '',
            'body' => 'Body',
            'published' => false,
            'tag_ids' => [],
        ];

        // Assert
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Slug cannot be empty');

        // Act
        Article::create($data);
    }

    /**
     * Test create with too long slug throws exception
     *
     * @return void
     */
    public function testCreateWithTooLongSlugThrowsException(): void
    {
        // Arrange
        $data = [
            'user_id' => 1,
            'title' => 'Test',
            'slug' => str_repeat('a', 192),
            'body' => 'Body',
            'published' => false,
            'tag_ids' => [],
        ];

        // Assert
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Slug cannot exceed 191 characters');

        // Act
        Article::create($data);
    }

    /**
     * Test create with invalid slug format throws exception
     *
     * @return void
     */
    public function testCreateWithInvalidSlugFormatThrowsException(): void
    {
        // Arrange
        $data = [
            'user_id' => 1,
            'title' => 'Test',
            'slug' => 'Invalid Slug',
            'body' => 'Body',
            'published' => false,
            'tag_ids' => [],
        ];

        // Assert
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Slug can only contain lowercase letters, numbers, and hyphens');

        // Act
        Article::create($data);
    }

    /**
     * Test create with uppercase in slug throws exception
     *
     * @return void
     */
    public function testCreateWithUppercaseInSlugThrowsException(): void
    {
        // Arrange
        $data = [
            'user_id' => 1,
            'title' => 'Test',
            'slug' => 'Test-Article',
            'body' => 'Body',
            'published' => false,
            'tag_ids' => [],
        ];

        // Assert
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Slug can only contain lowercase letters, numbers, and hyphens');

        // Act
        Article::create($data);
    }

    /**
     * Test create with empty body throws exception
     *
     * @return void
     */
    public function testCreateWithEmptyBodyThrowsException(): void
    {
        // Arrange
        $data = [
            'user_id' => 1,
            'title' => 'Test',
            'slug' => 'test-article',
            'body' => '',
            'published' => false,
            'tag_ids' => [],
        ];

        // Assert
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Body cannot be empty');

        // Act
        Article::create($data);
    }

    /**
     * Test reconstruct with full data
     *
     * @return void
     */
    public function testReconstructWithFullData(): void
    {
        // Arrange
        $data = [
            'id' => 1,
            'user_id' => 2,
            'title' => 'Reconstructed Article',
            'slug' => 'reconstructed-article',
            'body' => 'This is the body.',
            'published' => true,
            'tag_ids' => [4, 5],
            'created' => '2026-01-01 10:00:00',
            'modified' => '2026-01-15 15:30:00',
        ];

        // Act
        $article = Article::reconstruct($data);

        // Assert
        $this->assertInstanceOf(Article::class, $article);
        $this->assertInstanceOf(ArticleId::class, $article->id);
        $this->assertSame(1, $article->id->value);
        $this->assertSame(2, $article->userId);
        $this->assertSame('Reconstructed Article', $article->title);
        $this->assertSame('reconstructed-article', $article->slug);
        $this->assertSame('This is the body.', $article->body);
        $this->assertTrue($article->published, 'Article should be published');
        $this->assertSame([4, 5], $article->tagIds);
        $this->assertInstanceOf(DateTimeImmutable::class, $article->created);
        $this->assertSame('2026-01-01 10:00:00', $article->created->format('Y-m-d H:i:s'));
        $this->assertInstanceOf(DateTimeImmutable::class, $article->modified);
        $this->assertSame('2026-01-15 15:30:00', $article->modified->format('Y-m-d H:i:s'));
    }

    /**
     * Test reconstruct without id
     *
     * @return void
     */
    public function testReconstructWithoutId(): void
    {
        // Arrange
        $data = [
            'user_id' => 1,
            'title' => 'No ID Article',
            'slug' => 'no-id',
            'body' => 'Body',
            'published' => false,
            'tag_ids' => [],
        ];

        // Act
        $article = Article::reconstruct($data);

        // Assert
        $this->assertNull($article->id, 'Reconstructed article without ID should have null ID');
    }

    /**
     * Test update title
     *
     * @return void
     */
    public function testUpdateTitle(): void
    {
        // Arrange
        $original = Article::create([
            'user_id' => 1,
            'title' => 'Original Title',
            'slug' => 'original',
            'body' => 'Body',
            'published' => false,
            'tag_ids' => [],
        ]);

        // Act
        $updated = $original->update([
            'title' => 'Updated Title',
        ]);

        // Assert
        $this->assertNotSame($original, $updated, 'Update should return a new instance');
        $this->assertSame('Updated Title', $updated->title);
        $this->assertSame('original', $updated->slug);
        $this->assertSame('Original Title', $original->title, 'Original should remain unchanged');
    }

    /**
     * Test update slug
     *
     * @return void
     */
    public function testUpdateSlug(): void
    {
        // Arrange
        $original = Article::create([
            'user_id' => 1,
            'title' => 'Title',
            'slug' => 'original-slug',
            'body' => 'Body',
            'published' => false,
            'tag_ids' => [],
        ]);

        // Act
        $updated = $original->update([
            'slug' => 'updated-slug',
        ]);

        // Assert
        $this->assertSame('updated-slug', $updated->slug);
        $this->assertSame('original-slug', $original->slug, 'Original should remain unchanged');
    }

    /**
     * Test update body
     *
     * @return void
     */
    public function testUpdateBody(): void
    {
        // Arrange
        $original = Article::create([
            'user_id' => 1,
            'title' => 'Title',
            'slug' => 'slug',
            'body' => 'Original body',
            'published' => false,
            'tag_ids' => [],
        ]);

        // Act
        $updated = $original->update([
            'body' => 'Updated body',
        ]);

        // Assert
        $this->assertSame('Updated body', $updated->body);
        $this->assertSame('Original body', $original->body, 'Original should remain unchanged');
    }

    /**
     * Test update published status
     *
     * @return void
     */
    public function testUpdatePublishedStatus(): void
    {
        // Arrange
        $original = Article::create([
            'user_id' => 1,
            'title' => 'Title',
            'slug' => 'slug',
            'body' => 'Body',
            'published' => false,
            'tag_ids' => [],
        ]);

        // Act
        $updated = $original->update([
            'published' => true,
        ]);

        // Assert
        $this->assertTrue($updated->published, 'Updated article should be published');
        $this->assertFalse($original->published, 'Original should remain unchanged');
    }

    /**
     * Test update tag IDs
     *
     * @return void
     */
    public function testUpdateTagIds(): void
    {
        // Arrange
        $original = Article::create([
            'user_id' => 1,
            'title' => 'Title',
            'slug' => 'slug',
            'body' => 'Body',
            'published' => false,
            'tag_ids' => [1, 2],
        ]);

        // Act
        $updated = $original->update([
            'tag_ids' => [3, 4, 5],
        ]);

        // Assert
        $this->assertSame([3, 4, 5], $updated->tagIds);
        $this->assertSame([1, 2], $original->tagIds, 'Original should remain unchanged');
    }

    /**
     * Test update multiple fields
     *
     * @return void
     */
    public function testUpdateMultipleFields(): void
    {
        // Arrange
        $original = Article::create([
            'user_id' => 1,
            'title' => 'Original',
            'slug' => 'original',
            'body' => 'Original body',
            'published' => false,
            'tag_ids' => [],
        ]);

        // Act
        $updated = $original->update([
            'title' => 'Updated',
            'slug' => 'updated',
            'body' => 'Updated body',
            'published' => true,
        ]);

        // Assert
        $this->assertSame('Updated', $updated->title);
        $this->assertSame('updated', $updated->slug);
        $this->assertSame('Updated body', $updated->body);
        $this->assertTrue($updated->published, 'Article should be published');
    }

    /**
     * Test update with invalid title throws exception
     *
     * @return void
     */
    public function testUpdateWithInvalidTitleThrowsException(): void
    {
        // Arrange
        $original = Article::create([
            'user_id' => 1,
            'title' => 'Valid',
            'slug' => 'valid',
            'body' => 'Body',
            'published' => false,
            'tag_ids' => [],
        ]);

        // Assert
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Title cannot be empty');

        // Act
        $original->update([
            'title' => '',
        ]);
    }

    /**
     * Test update preserves timestamps
     *
     * @return void
     */
    public function testUpdatePreservesTimestamps(): void
    {
        // Arrange
        $original = Article::reconstruct([
            'id' => 1,
            'user_id' => 1,
            'title' => 'Title',
            'slug' => 'slug',
            'body' => 'Body',
            'published' => false,
            'tag_ids' => [],
            'created' => '2026-01-01 10:00:00',
            'modified' => '2026-01-15 15:30:00',
        ]);

        // Act
        $updated = $original->update([
            'title' => 'Updated Title',
        ]);

        // Assert
        $this->assertSame(
            $original->created?->format('Y-m-d H:i:s'),
            $updated->created?->format('Y-m-d H:i:s'),
            'Created timestamp should be preserved',
        );
        $this->assertSame(
            $original->modified?->format('Y-m-d H:i:s'),
            $updated->modified?->format('Y-m-d H:i:s'),
            'Modified timestamp should be preserved',
        );
    }

    /**
     * Test Article is final
     *
     * @return void
     */
    public function testArticleIsFinal(): void
    {
        // Arrange & Act
        $reflection = new ReflectionClass(Article::class);

        // Assert
        $this->assertTrue($reflection->isFinal(), 'Article class should be declared as final');
    }

    /**
     * Test Article is readonly
     *
     * @return void
     */
    public function testArticleIsReadonly(): void
    {
        // Arrange & Act
        $reflection = new ReflectionClass(Article::class);

        // Assert
        $this->assertTrue($reflection->isReadOnly(), 'Article class should be declared as readonly');
    }
}
