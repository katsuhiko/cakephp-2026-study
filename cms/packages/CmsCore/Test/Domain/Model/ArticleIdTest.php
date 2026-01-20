<?php
declare(strict_types=1);

namespace CmsCore\Test\Domain\Model;

use CmsCore\Domain\Model\ArticleId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * ArticleId Test Case
 */
class ArticleIdTest extends TestCase
{
    /**
     * Test fromInt with valid value
     *
     * @return void
     */
    public function testFromIntWithValidValue(): void
    {
        // Arrange & Act
        $articleId = ArticleId::fromInt(1);

        // Assert
        $this->assertInstanceOf(ArticleId::class, $articleId);
        $this->assertSame(1, $articleId->value);
    }

    /**
     * Test fromInt with large value
     *
     * @return void
     */
    public function testFromIntWithLargeValue(): void
    {
        // Arrange & Act
        $articleId = ArticleId::fromInt(999999);

        // Assert
        $this->assertSame(999999, $articleId->value);
    }

    /**
     * Test fromInt with zero throws exception
     *
     * @return void
     */
    public function testFromIntWithZeroThrowsException(): void
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Article ID must be greater than 0');

        // Act
        ArticleId::fromInt(0);
    }

    /**
     * Test fromInt with negative value throws exception
     *
     * @return void
     */
    public function testFromIntWithNegativeValueThrowsException(): void
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Article ID must be greater than 0');

        // Act
        ArticleId::fromInt(-1);
    }

    /**
     * Test equals with same value
     *
     * @return void
     */
    public function testEqualsWithSameValue(): void
    {
        // Arrange
        $articleId1 = ArticleId::fromInt(1);
        $articleId2 = ArticleId::fromInt(1);

        // Act
        $result = $articleId1->equals($articleId2);

        // Assert
        $this->assertTrue($result, 'ArticleIds with the same value should be equal');
    }

    /**
     * Test equals with different value
     *
     * @return void
     */
    public function testEqualsWithDifferentValue(): void
    {
        // Arrange
        $articleId1 = ArticleId::fromInt(1);
        $articleId2 = ArticleId::fromInt(2);

        // Act
        $result = $articleId1->equals($articleId2);

        // Assert
        $this->assertFalse($result, 'ArticleIds with different values should not be equal');
    }

    /**
     * Test equals with same instance
     *
     * @return void
     */
    public function testEqualsWithSameInstance(): void
    {
        // Arrange
        $articleId = ArticleId::fromInt(1);

        // Act
        $result = $articleId->equals($articleId);

        // Assert
        $this->assertTrue($result, 'ArticleId should be equal to itself');
    }

    /**
     * Test readonly property - value cannot be modified
     *
     * @return void
     */
    public function testValueIsReadonly(): void
    {
        // Arrange & Act
        $reflection = new ReflectionClass(ArticleId::class);

        // Assert
        $this->assertTrue($reflection->isReadOnly(), 'ArticleId class should be declared as readonly');
    }

    /**
     * Test that ArticleId is final
     *
     * @return void
     */
    public function testArticleIdIsFinal(): void
    {
        // Arrange & Act
        $reflection = new ReflectionClass(ArticleId::class);

        // Assert
        $this->assertTrue($reflection->isFinal(), 'ArticleId class should be declared as final');
    }
}
