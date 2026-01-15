<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\User;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Entity\User Test Case
 */
class UserTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Entity\User
     */
    protected $User;

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->User = new User();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->User);
        parent::tearDown();
    }

    /**
     * Test password hashing on entity creation
     *
     * @return void
     */
    public function testPasswordHashing(): void
    {
        $user = new User([
            'email' => 'test@example.com',
            'password' => 'secret',
        ]);

        $this->assertNotEquals('secret', $user->password);
        $this->assertTrue(password_verify('secret', $user->password), 'Password should be hashed correctly');
    }

    /**
     * Test password hashing when updating
     *
     * @return void
     */
    public function testPasswordHashingOnUpdate(): void
    {
        $user = new User([
            'email' => 'test@example.com',
            'password' => 'original',
        ]);

        $originalHash = $user->password;

        $user->password = 'updated';
        $this->assertNotEquals($originalHash, $user->password);
        $this->assertTrue(password_verify('updated', $user->password), 'Updated password should be hashed correctly');
    }

    /**
     * Test that empty password returns null
     *
     * @return void
     */
    public function testEmptyPassword(): void
    {
        $user = new User([
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $this->assertNull($user->password, 'Empty password should return null');
    }

    /**
     * Test accessible fields
     *
     * @return void
     */
    public function testAccessibleFields(): void
    {
        $data = [
            'id' => 999,
            'email' => 'test@example.com',
            'password' => 'secret',
            'created' => '2026-01-15 00:00:00',
            'modified' => '2026-01-15 00:00:00',
        ];

        $user = new User($data);

        $this->assertEquals(999, $user->id);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertNotNull($user->password, 'Password field should be accessible');
        $this->assertNotNull($user->created, 'Created field should be accessible');
        $this->assertNotNull($user->modified, 'Modified field should be accessible');
    }

    /**
     * Test that password is hidden in JSON
     *
     * @return void
     */
    public function testPasswordHiddenInJson(): void
    {
        $user = new User([
            'email' => 'test@example.com',
            'password' => 'secret',
        ]);

        $json = json_decode(json_encode($user), true);

        $this->assertArrayHasKey('email', $json, 'Email should be included in JSON');
        $this->assertArrayNotHasKey('password', $json, 'Password should be hidden in JSON');
    }

    /**
     * Test that password is hidden in array conversion
     *
     * @return void
     */
    public function testPasswordHiddenInArray(): void
    {
        $user = new User([
            'id' => 1,
            'email' => 'test@example.com',
            'password' => 'secret',
        ]);

        $array = $user->toArray();

        $this->assertArrayHasKey('email', $array, 'Email should be included in array');
        $this->assertArrayNotHasKey('password', $array, 'Password should be hidden in array');
    }

    /**
     * Test password hashing with special characters
     *
     * @return void
     */
    public function testPasswordHashingWithSpecialCharacters(): void
    {
        $specialPassword = 'p@ssw0rd!#$%^&*()';
        $user = new User([
            'email' => 'test@example.com',
            'password' => $specialPassword,
        ]);

        $this->assertTrue(password_verify($specialPassword, $user->password), 'Password with special characters should be hashed correctly');
    }

    /**
     * Test that different passwords produce different hashes
     *
     * @return void
     */
    public function testDifferentPasswordsProduceDifferentHashes(): void
    {
        $user1 = new User(['password' => 'password1']);
        $user2 = new User(['password' => 'password2']);

        $this->assertNotEquals($user1->password, $user2->password);
    }
}
