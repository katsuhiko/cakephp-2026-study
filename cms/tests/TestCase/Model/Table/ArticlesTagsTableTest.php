<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ArticlesTagsTable;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * App\Model\Table\ArticlesTagsTable Test Case
 */
class ArticlesTagsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ArticlesTagsTable
     */
    protected $ArticlesTags;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.ArticlesTags',
        'app.Articles',
        'app.Users',
        'app.Tags',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('ArticlesTags') ? [] : ['className' => ArticlesTagsTable::class];
        $this->ArticlesTags = $this->getTableLocator()->get('ArticlesTags', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->ArticlesTags);

        parent::tearDown();
    }

    /**
     * Data provider for testBuildRules
     *
     * @return array<string, array<string, mixed>>
     */
    public static function buildRulesProvider(): array
    {
        return [
            'valid article and tag should succeed' => [
                'articleId' => '1',
                'tagId' => '1',
                'shouldSucceed' => true,
                'errorField' => null,
                'errorKey' => null,
            ],
            'non-existent article_id should fail' => [
                'articleId' => '999',
                'tagId' => '1',
                'shouldSucceed' => false,
                'errorField' => 'article_id',
                'errorKey' => '_existsIn',
            ],
            'non-existent tag_id should fail' => [
                'articleId' => '1',
                'tagId' => '999',
                'shouldSucceed' => false,
                'errorField' => 'tag_id',
                'errorKey' => '_existsIn',
            ],
        ];
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\ArticlesTagsTable::buildRules()
     */
    #[DataProvider('buildRulesProvider')]
    public function testBuildRules(
        string $articleId,
        string $tagId,
        bool $shouldSucceed,
        ?string $errorField,
        ?string $errorKey
    ): void {
        // Arrange
        $articlesTag = $this->ArticlesTags->newEntity([
            'article_id' => $articleId,
            'tag_id' => $tagId,
        ]);

        // Act
        $result = $this->ArticlesTags->save($articlesTag);

        // Assert
        if ($shouldSucceed) {
            $this->assertNotFalse($result, 'Save should succeed with valid data. Errors: ' . json_encode($articlesTag->getErrors()));
            $this->assertEmpty($articlesTag->getErrors(), 'ArticlesTag entity should not have errors. Errors: ' . json_encode($articlesTag->getErrors()));
        } else {
            $this->assertFalse($result, 'Save should fail with invalid data. Errors: ' . json_encode($articlesTag->getErrors()));
            $this->assertNotEmpty($articlesTag->getErrors(), 'Invalid data should fail validation. Errors: ' . json_encode($articlesTag->getErrors()));
            $this->assertArrayHasKey($errorField, $articlesTag->getErrors(), "Error should exist for {$errorField} field. Errors: " . json_encode($articlesTag->getErrors()));
            $this->assertArrayHasKey($errorKey, $articlesTag->getErrors()[$errorField], "Error key {$errorKey} should exist. Errors: " . json_encode($articlesTag->getErrors()));
        }
    }
}
