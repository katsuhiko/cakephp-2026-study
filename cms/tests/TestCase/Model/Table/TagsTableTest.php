<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TagsTable;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * App\Model\Table\TagsTable Test Case
 */
class TagsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TagsTable
     */
    protected $Tags;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Tags',
        'app.Articles',
        'app.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Tags') ? [] : ['className' => TagsTable::class];
        $this->Tags = $this->getTableLocator()->get('Tags', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Tags);

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
                'title' => 'Test Tag',
                'shouldSucceed' => true,
                'errorField' => null,
                'errorKey' => null,
            ],
            'empty title' => [
                'title' => '',
                'shouldSucceed' => false,
                'errorField' => 'title',
                'errorKey' => '_empty',
            ],
            'title exceeding max length' => [
                'title' => str_repeat('a', 192),
                'shouldSucceed' => false,
                'errorField' => 'title',
                'errorKey' => 'maxLength',
            ],
        ];
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\TagsTable::validationDefault()
     */
    #[DataProvider('validationDefaultProvider')]
    public function testValidationDefault(
        string $title,
        bool $shouldSucceed,
        ?string $errorField,
        ?string $errorKey,
    ): void {
        // Arrange
        $tag = $this->Tags->newEntity([
            'title' => $title,
        ]);

        // Assert
        if ($shouldSucceed) {
            $this->assertEmpty($tag->getErrors(), 'Valid data should pass validation. Errors: ' . json_encode($tag->getErrors()));
        } else {
            $this->assertNotEmpty($tag->getErrors(), 'Invalid data should fail validation. Errors: ' . json_encode($tag->getErrors()));
            $this->assertArrayHasKey($errorField, $tag->getErrors(), "Error should exist for {$errorField} field. Errors: " . json_encode($tag->getErrors()));
            $this->assertArrayHasKey($errorKey, $tag->getErrors()[$errorField], "Error key {$errorKey} should exist. Errors: " . json_encode($tag->getErrors()));
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
            'unique title should succeed' => [
                'title' => 'New Tag',
                'shouldSucceed' => true,
                'errorField' => null,
                'errorKey' => null,
            ],
            'duplicate title should fail' => [
                'title' => 'First Tag',
                'shouldSucceed' => false,
                'errorField' => 'title',
                'errorKey' => 'unique',
            ],
        ];
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\TagsTable::buildRules()
     */
    #[DataProvider('buildRulesProvider')]
    public function testBuildRules(
        string $title,
        bool $shouldSucceed,
        ?string $errorField,
        ?string $errorKey,
    ): void {
        // Arrange
        $tag = $this->Tags->newEntity([
            'title' => $title,
        ]);

        // Act
        $result = $this->Tags->save($tag);

        // Assert
        if ($shouldSucceed) {
            $this->assertNotFalse($result, 'Save should succeed with valid data. Errors: ' . json_encode($tag->getErrors()));
            $this->assertEmpty($tag->getErrors(), 'Tag entity should not have errors. Errors: ' . json_encode($tag->getErrors()));
        } else {
            $this->assertFalse($result, 'Save should fail with invalid data. Errors: ' . json_encode($tag->getErrors()));
            $this->assertNotEmpty($tag->getErrors(), 'Invalid data should fail validation. Errors: ' . json_encode($tag->getErrors()));
            $this->assertArrayHasKey($errorField, $tag->getErrors(), "Error should exist for {$errorField} field. Errors: " . json_encode($tag->getErrors()));
            $this->assertArrayHasKey($errorKey, $tag->getErrors()[$errorField], "Error key {$errorKey} should exist. Errors: " . json_encode($tag->getErrors()));
        }
    }
}
