<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\UsersController Test Case
 *
 * @link \App\Controller\UsersController
 */
class UsersControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Users',
        'app.Articles',
    ];

    /**
     * Test index method
     *
     * @return void
     * @link \App\Controller\UsersController::index()
     */
    public function testIndex(): void
    {
        // Act
        $this->get('/users');

        // Assert
        $this->assertResponseOk();
        $users = $this->viewVariable('users');
        $this->assertInstanceOf('Cake\Datasource\Paging\PaginatedResultSet', $users);
    }

    /**
     * Test view method
     *
     * @return void
     * @link \App\Controller\UsersController::view()
     */
    public function testView(): void
    {
        // Act
        $this->get('/users/view/1');

        // Assert
        $this->assertResponseOk();
        $user = $this->viewVariable('user');
        $this->assertInstanceOf('App\Model\Entity\User', $user);
        $this->assertEquals(1, $user->id);

        // Assert that articles are loaded
        $this->assertNotEmpty($user->articles, 'User should have related articles');
        $article = $user->articles[0];
        $this->assertInstanceOf('App\Model\Entity\Article', $article);
    }

    /**
     * Test add method (GET)
     *
     * @return void
     * @link \App\Controller\UsersController::add()
     */
    public function testAddGet(): void
    {
        // Act
        $this->get('/users/add');

        // Assert
        $this->assertResponseOk();
        $user = $this->viewVariable('user');
        $this->assertInstanceOf('App\Model\Entity\User', $user);
    }

    /**
     * Test add method (POST)
     *
     * @return void
     * @link \App\Controller\UsersController::add()
     */
    public function testAddPost(): void
    {
        // Arrange
        $this->enableCsrfToken();

        $data = [
            'email' => 'newuser@example.com',
            'password' => 'password123',
        ];

        // Act
        $this->post('/users/add', $data);

        // Assert
        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Users', 'action' => 'index']);
        $this->assertFlashMessage('The user has been saved.');

        // Verify the user was actually saved
        $users = $this->getTableLocator()->get('Users');
        $query = $users->find()->where(['email' => 'newuser@example.com']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test edit method (GET)
     *
     * @return void
     * @link \App\Controller\UsersController::edit()
     */
    public function testEditGet(): void
    {
        // Act
        $this->get('/users/edit/1');

        // Assert
        $this->assertResponseOk();
        $user = $this->viewVariable('user');
        $this->assertInstanceOf('App\Model\Entity\User', $user);
        $this->assertEquals(1, $user->id);
    }

    /**
     * Test edit method (POST)
     *
     * @return void
     * @link \App\Controller\UsersController::edit()
     */
    public function testEditPost(): void
    {
        // Arrange
        $this->enableCsrfToken();

        $data = [
            'email' => 'updated@example.com',
        ];

        // Act
        $this->post('/users/edit/1', $data);

        // Assert
        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Users', 'action' => 'index']);
        $this->assertFlashMessage('The user has been saved.');

        // Verify the user was actually updated
        $users = $this->getTableLocator()->get('Users');
        $user = $users->get(1);
        $this->assertEquals('updated@example.com', $user->email);
    }

    /**
     * Test changePassword method (GET)
     *
     * @return void
     * @link \App\Controller\UsersController::changePassword()
     */
    public function testChangePasswordGet(): void
    {
        // Act
        $this->get('/users/change-password/1');

        // Assert
        $this->assertResponseOk();
        $user = $this->viewVariable('user');
        $this->assertInstanceOf('App\Model\Entity\User', $user);
        $this->assertEquals(1, $user->id);
    }

    /**
     * Test changePassword method (POST)
     *
     * @return void
     * @link \App\Controller\UsersController::changePassword()
     */
    public function testChangePasswordPost(): void
    {
        // Arrange
        $this->enableCsrfToken();

        $data = [
            'password' => 'newpassword123',
        ];

        // Act
        $this->post('/users/change-password/1', $data);

        // Assert
        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Users', 'action' => 'index']);
        $this->assertFlashMessage('The password has been changed.');

        // Verify the password was actually updated and hashed correctly
        $users = $this->getTableLocator()->get('Users');
        $user = $users->get(1);
        $this->assertTrue(
            password_verify('newpassword123', $user->password),
            'Password was not hashed correctly or does not match the expected value',
        );
    }

    /**
     * Test delete method
     *
     * @return void
     * @link \App\Controller\UsersController::delete()
     */
    public function testDelete(): void
    {
        // Arrange
        $this->enableCsrfToken();

        // Act
        $this->post('/users/delete/1');

        // Assert
        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Users', 'action' => 'index']);
        $this->assertFlashMessage('The user has been deleted.');

        // Verify the user was actually deleted
        $users = $this->getTableLocator()->get('Users');
        $query = $users->find()->where(['id' => 1]);
        $this->assertEquals(0, $query->count());
    }
}
