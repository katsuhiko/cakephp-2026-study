<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ArticlesTable;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * App\Model\Table\ArticlesTable Test Case
 */
class ArticlesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ArticlesTable
     */
    protected $Articles;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
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
        $config = $this->getTableLocator()->exists('Articles') ? [] : ['className' => ArticlesTable::class];
        $this->Articles = $this->getTableLocator()->get('Articles', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Articles);

        parent::tearDown();
    }

    /**
     * Data provider for testValidationDefault
     *
     * @return array<string, array<string, mixed>>
     */
    public static function validationDefaultProvider(): array
    {
        return [
            'valid data' => [
                'userId' => '1',
                'title' => 'Test Article',
                'slug' => 'test-article',
                'body' => 'Test body content',
                'published' => '1',
                'shouldSucceed' => true,
                'errorField' => null,
                'errorKey' => null,
            ],
            'empty title' => [
                'userId' => '1',
                'title' => '',
                'slug' => 'test-article',
                'body' => 'Test body content',
                'published' => '1',
                'shouldSucceed' => false,
                'errorField' => 'title',
                'errorKey' => '_empty',
            ],
            'title exceeding max length' => [
                'userId' => '1',
                'title' => str_repeat('a', 256),
                'slug' => 'test-article',
                'body' => 'Test body content',
                'published' => '1',
                'shouldSucceed' => false,
                'errorField' => 'title',
                'errorKey' => 'maxLength',
            ],
            'empty slug' => [
                'userId' => '1',
                'title' => 'Test Article',
                'slug' => '',
                'body' => 'Test body content',
                'published' => '1',
                'shouldSucceed' => false,
                'errorField' => 'slug',
                'errorKey' => '_empty',
            ],
            'slug exceeding max length' => [
                'userId' => '1',
                'title' => 'Test Article',
                'slug' => str_repeat('a', 192),
                'body' => 'Test body content',
                'published' => '1',
                'shouldSucceed' => false,
                'errorField' => 'slug',
                'errorKey' => 'maxLength',
            ],
            'empty user_id' => [
                'userId' => '',
                'title' => 'Test Article',
                'slug' => 'test-article',
                'body' => 'Test body content',
                'published' => '1',
                'shouldSucceed' => false,
                'errorField' => 'user_id',
                'errorKey' => '_empty',
            ],
        ];
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\ArticlesTable::validationDefault()
     */
    #[DataProvider('validationDefaultProvider')]
    public function testValidationDefault(
        string $userId,
        string $title,
        string $slug,
        string $body,
        string $published,
        bool $shouldSucceed,
        ?string $errorField,
        ?string $errorKey
    ): void {
        // Arrange
        $article = $this->Articles->newEntity([
            'user_id' => $userId,
            'title' => $title,
            'slug' => $slug,
            'body' => $body,
            'published' => $published,
        ]);

        // Assert
        if ($shouldSucceed) {
            $this->assertEmpty($article->getErrors(), 'Valid data should pass validation. Errors: ' . json_encode($article->getErrors()));
        } else {
            $this->assertNotEmpty($article->getErrors(), 'Invalid data should fail validation. Errors: ' . json_encode($article->getErrors()));
            $this->assertArrayHasKey($errorField, $article->getErrors(), "Error should exist for {$errorField} field. Errors: " . json_encode($article->getErrors()));
            $this->assertArrayHasKey($errorKey, $article->getErrors()[$errorField], "Error key {$errorKey} should exist. Errors: " . json_encode($article->getErrors()));
        }
    }

    /**
     * Data provider for testBuildRules
     *
     * @return array<string, array<string, mixed>>
     */
    public static function buildRulesProvider(): array
    {
        return [
            'unique slug should succeed' => [
                'userId' => '1',
                'title' => 'New Article',
                'slug' => 'new-slug',
                'body' => 'Test body content',
                'published' => '1',
                'shouldSucceed' => true,
                'errorField' => null,
                'errorKey' => null,
            ],
            'duplicate slug should fail' => [
                'userId' => '1',
                'title' => 'New Article',
                'slug' => 'first-slug',
                'body' => 'Test body content',
                'published' => '1',
                'shouldSucceed' => false,
                'errorField' => 'slug',
                'errorKey' => 'unique',
            ],
            'non-existent user_id should fail' => [
                'userId' => '999',
                'title' => 'New Article',
                'slug' => 'new-slug',
                'body' => 'Test body content',
                'published' => '1',
                'shouldSucceed' => false,
                'errorField' => 'user_id',
                'errorKey' => '_existsIn',
            ],
        ];
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\ArticlesTable::buildRules()
     */
    #[DataProvider('buildRulesProvider')]
    public function testBuildRules(
        string $userId,
        string $title,
        string $slug,
        string $body,
        string $published,
        bool $shouldSucceed,
        ?string $errorField,
        ?string $errorKey
    ): void {
        // Arrange
        $article = $this->Articles->newEntity([
            'user_id' => $userId,
            'title' => $title,
            'slug' => $slug,
            'body' => $body,
            'published' => $published,
        ]);

        // Act
        $result = $this->Articles->save($article);

        // Assert
        if ($shouldSucceed) {
            $this->assertNotFalse($result, 'Save should succeed with valid data. Errors: ' . json_encode($article->getErrors()));
            $this->assertEmpty($article->getErrors(), 'Article entity should not have errors. Errors: ' . json_encode($article->getErrors()));
        } else {
            $this->assertFalse($result, 'Save should fail with invalid data. Errors: ' . json_encode($article->getErrors()));
            $this->assertNotEmpty($article->getErrors(), 'Invalid data should fail validation. Errors: ' . json_encode($article->getErrors()));
            $this->assertArrayHasKey($errorField, $article->getErrors(), "Error should exist for {$errorField} field. Errors: " . json_encode($article->getErrors()));
            $this->assertArrayHasKey($errorKey, $article->getErrors()[$errorField], "Error key {$errorKey} should exist. Errors: " . json_encode($article->getErrors()));
        }
    }
}
