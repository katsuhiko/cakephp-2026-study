<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\UsersTable;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * App\Model\Table\UsersTable Test Case
 */
class UsersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\UsersTable
     */
    protected $Users;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
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
        $config = $this->getTableLocator()->exists('Users') ? [] : ['className' => UsersTable::class];
        $this->Users = $this->getTableLocator()->get('Users', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Users);

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
            'valid email and password' => [
                'email' => 'test@example.com',
                'password' => 'password123',
                'shouldSucceed' => true,
                'errorField' => null,
                'errorKey' => null,
            ],
            'empty email' => [
                'email' => '',
                'password' => 'password123',
                'shouldSucceed' => false,
                'errorField' => 'email',
                'errorKey' => '_empty',
            ],
            'invalid email format' => [
                'email' => 'invalid-email',
                'password' => 'password123',
                'shouldSucceed' => false,
                'errorField' => 'email',
                'errorKey' => 'email',
            ],
            'empty password' => [
                'email' => 'test@example.com',
                'password' => '',
                'shouldSucceed' => false,
                'errorField' => 'password',
                'errorKey' => '_empty',
            ],
            'password exceeding max length' => [
                'email' => 'test@example.com',
                'password' => str_repeat('a', 256),
                'shouldSucceed' => false,
                'errorField' => 'password',
                'errorKey' => 'maxLength',
            ],
        ];
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\UsersTable::validationDefault()
     */
    #[DataProvider('validationDefaultProvider')]
    public function testValidationDefault(
        string $email,
        string $password,
        bool $shouldSucceed,
        ?string $errorField,
        ?string $errorKey
    ): void {
        // Arrange
        $user = $this->Users->newEntity([
            'email' => $email,
            'password' => $password,
        ]);

        // Assert
        if ($shouldSucceed) {
            $this->assertEmpty($user->getErrors(), 'Valid data should pass validation. Errors: ' . json_encode($user->getErrors()));
        } else {
            $this->assertNotEmpty($user->getErrors(), 'Invalid data should fail validation. Errors: ' . json_encode($user->getErrors()));
            $this->assertArrayHasKey($errorField, $user->getErrors(), "Error should exist for {$errorField} field. Errors: " . json_encode($user->getErrors()));
            $this->assertArrayHasKey($errorKey, $user->getErrors()[$errorField], "Error key {$errorKey} should exist. Errors: " . json_encode($user->getErrors()));
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
            'unique email should succeed' => [
                'email' => 'newuser@example.com',
                'password' => 'password123',
                'shouldSucceed' => true,
                'errorField' => null,
                'errorKey' => null,
            ],
            'duplicate email should fail' => [
                'email' => 'user1@example.com',
                'password' => 'password123',
                'shouldSucceed' => false,
                'errorField' => 'email',
                'errorKey' => 'unique',
            ],
        ];
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\UsersTable::buildRules()
     */
    #[DataProvider('buildRulesProvider')]
    public function testBuildRules(
        string $email,
        string $password,
        bool $shouldSucceed,
        ?string $errorField,
        ?string $errorKey
    ): void {
        // Arrange
        $user = $this->Users->newEntity([
            'email' => $email,
            'password' => $password,
        ]);

        // Act
        $result = $this->Users->save($user);

        // Assert
        if ($shouldSucceed) {
            $this->assertNotFalse($result, 'Save should succeed with unique email. Errors: ' . json_encode($user->getErrors()));
            $this->assertEmpty($user->getErrors(), 'User entity should not have errors. Errors: ' . json_encode($user->getErrors()));
        } else {
            $this->assertFalse($result, 'Save should fail with duplicate email. Errors: ' . json_encode($user->getErrors()));
            $this->assertNotEmpty($user->getErrors(), 'Invalid data should fail validation. Errors: ' . json_encode($user->getErrors()));
            $this->assertArrayHasKey($errorField, $user->getErrors(), "Error should exist for {$errorField} field. Errors: " . json_encode($user->getErrors()));
            $this->assertArrayHasKey($errorKey, $user->getErrors()[$errorField], "Error key {$errorKey} should exist. Errors: " . json_encode($user->getErrors()));
        }
    }
}
