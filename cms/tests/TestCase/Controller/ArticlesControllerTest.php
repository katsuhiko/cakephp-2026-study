<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\ArticlesController Test Case
 *
 * @link \App\Controller\ArticlesController
 */
class ArticlesControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Articles',
        'app.Users',
        'app.Tags',
        'app.ArticlesTags',
    ];

    /**
     * Test index method
     *
     * @return void
     * @link \App\Controller\ArticlesController::index()
     */
    public function testIndex(): void
    {
        // Act
        $this->get('/articles');

        // Assert
        $this->assertResponseOk();
        $articles = $this->viewVariable('articles');
        $this->assertInstanceOf('Cake\Datasource\Paging\PaginatedResultSet', $articles);
    }

    /**
     * Test view method
     *
     * @return void
     * @link \App\Controller\ArticlesController::view()
     */
    public function testView(): void
    {
        // Act
        $this->get('/articles/view/1');

        // Assert
        $this->assertResponseOk();
        $article = $this->viewVariable('article');
        $this->assertInstanceOf('App\Model\Entity\Article', $article);
        $this->assertEquals(1, $article->id);

        // Assert that user is loaded
        $this->assertTrue($article->hasValue('user'), 'Article should have user loaded');
        $this->assertInstanceOf('App\Model\Entity\User', $article->user);

        // Assert that tags are loaded
        $this->assertNotEmpty($article->tags, 'Article should have related tags');
        $tag = $article->tags[0];
        $this->assertInstanceOf('App\Model\Entity\Tag', $tag);
    }

    /**
     * Test add method (GET)
     *
     * @return void
     * @link \App\Controller\ArticlesController::add()
     */
    public function testAddGet(): void
    {
        // Act
        $this->get('/articles/add');

        // Assert
        $this->assertResponseOk();
        $article = $this->viewVariable('article');
        $this->assertInstanceOf('App\Model\Entity\Article', $article);
    }

    /**
     * Test add method (POST)
     *
     * @return void
     * @link \App\Controller\ArticlesController::add()
     */
    public function testAddPost(): void
    {
        // Arrange
        $this->enableCsrfToken();

        $data = [
            'user_id' => '1',
            'title' => 'New Test Article',
            'slug' => 'new-test-article',
            'body' => 'This is a test article body.',
            'published' => '1',
            'tag_ids' => ['1'],
        ];

        // Act
        $this->post('/articles/add', $data);

        // Assert
        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Articles', 'action' => 'index']);
        $this->assertFlashMessage('The article has been saved.');

        // Verify the article was actually saved
        $articles = $this->getTableLocator()->get('Articles');
        $query = $articles->find()->where(['slug' => 'new-test-article']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test edit method (GET)
     *
     * @return void
     * @link \App\Controller\ArticlesController::edit()
     */
    public function testEditGet(): void
    {
        // Act
        $this->get('/articles/edit/1');

        // Assert
        $this->assertResponseOk();
        $article = $this->viewVariable('article');
        $this->assertInstanceOf('App\Model\Entity\Article', $article);
        $this->assertEquals(1, $article->id);
    }

    /**
     * Test edit method (POST)
     *
     * @return void
     * @link \App\Controller\ArticlesController::edit()
     */
    public function testEditPost(): void
    {
        // Arrange
        $this->enableCsrfToken();

        $data = [
            'title' => 'Updated Article Title',
            'slug' => 'updated-article-title',
            'body' => 'Updated article body content.',
        ];

        // Act
        $this->post('/articles/edit/1', $data);

        // Assert
        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Articles', 'action' => 'index']);
        $this->assertFlashMessage('The article has been saved.');

        // Verify the article was actually updated
        $articles = $this->getTableLocator()->get('Articles');
        $article = $articles->get(1);
        $this->assertEquals('Updated Article Title', $article->title);
    }

    /**
     * Test delete method
     *
     * @return void
     * @link \App\Controller\ArticlesController::delete()
     */
    public function testDelete(): void
    {
        // Arrange
        $this->enableCsrfToken();

        // Act
        $this->post('/articles/delete/1');

        // Assert
        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Articles', 'action' => 'index']);
        $this->assertFlashMessage('The article has been deleted.');

        // Verify the article was actually deleted
        $articles = $this->getTableLocator()->get('Articles');
        $query = $articles->find()->where(['id' => 1]);
        $this->assertEquals(0, $query->count());
    }
}
