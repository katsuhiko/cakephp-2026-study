<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\TagsController Test Case
 *
 * @link \App\Controller\TagsController
 */
class TagsControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Tags',
        'app.Users',
        'app.Articles',
        'app.ArticlesTags',
    ];

    /**
     * Test index method
     *
     * @return void
     * @link \App\Controller\TagsController::index()
     */
    public function testIndex(): void
    {
        // Act
        $this->get('/tags');

        // Assert
        $this->assertResponseOk();
        $tags = $this->viewVariable('tags');
        $this->assertInstanceOf('Cake\Datasource\Paging\PaginatedResultSet', $tags);
    }

    /**
     * Test view method
     *
     * @return void
     * @link \App\Controller\TagsController::view()
     */
    public function testView(): void
    {
        // Act
        $this->get('/tags/view/1');

        // Assert
        $this->assertResponseOk();
        $tag = $this->viewVariable('tag');
        $this->assertInstanceOf('App\Model\Entity\Tag', $tag);
        $this->assertEquals(1, $tag->id);

        // Assert that articles with users are loaded
        $this->assertNotEmpty($tag->articles, 'Tag should have related articles');
        $article = $tag->articles[0];
        $this->assertInstanceOf('App\Model\Entity\Article', $article);
        $this->assertTrue($article->hasValue('user'), 'Article should have user loaded');
        $this->assertInstanceOf('App\Model\Entity\User', $article->user);
    }

    /**
     * Test add method (GET)
     *
     * @return void
     * @link \App\Controller\TagsController::add()
     */
    public function testAddGet(): void
    {
        // Act
        $this->get('/tags/add');

        // Assert
        $this->assertResponseOk();
        $tag = $this->viewVariable('tag');
        $this->assertInstanceOf('App\Model\Entity\Tag', $tag);
    }

    /**
     * Test add method (POST)
     *
     * @return void
     * @link \App\Controller\TagsController::add()
     */
    public function testAddPost(): void
    {
        // Arrange
        $this->enableCsrfToken();

        $data = [
            'title' => 'New Test Tag',
        ];

        // Act
        $this->post('/tags/add', $data);

        // Assert
        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Tags', 'action' => 'index']);
        $this->assertFlashMessage('The tag has been saved.');

        // Verify the tag was actually saved
        $tags = $this->getTableLocator()->get('Tags');
        $query = $tags->find()->where(['title' => 'New Test Tag']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test edit method (GET)
     *
     * @return void
     * @link \App\Controller\TagsController::edit()
     */
    public function testEditGet(): void
    {
        // Act
        $this->get('/tags/edit/1');

        // Assert
        $this->assertResponseOk();
        $tag = $this->viewVariable('tag');
        $this->assertInstanceOf('App\Model\Entity\Tag', $tag);
        $this->assertEquals(1, $tag->id);
    }

    /**
     * Test edit method (POST)
     *
     * @return void
     * @link \App\Controller\TagsController::edit()
     */
    public function testEditPost(): void
    {
        // Arrange
        $this->enableCsrfToken();

        $data = [
            'title' => 'Updated Tag Title',
        ];

        // Act
        $this->post('/tags/edit/1', $data);

        // Assert
        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Tags', 'action' => 'index']);
        $this->assertFlashMessage('The tag has been saved.');

        // Verify the tag was actually updated
        $tags = $this->getTableLocator()->get('Tags');
        $tag = $tags->get(1);
        $this->assertEquals('Updated Tag Title', $tag->title);
    }

    /**
     * Test delete method
     *
     * @return void
     * @link \App\Controller\TagsController::delete()
     */
    public function testDelete(): void
    {
        // Arrange
        $this->enableCsrfToken();

        // Act
        $this->post('/tags/delete/1');

        // Assert
        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Tags', 'action' => 'index']);
        $this->assertFlashMessage('The tag has been deleted.');

        // Verify the tag was actually deleted
        $tags = $this->getTableLocator()->get('Tags');
        $query = $tags->find()->where(['id' => 1]);
        $this->assertEquals(0, $query->count());
    }
}
