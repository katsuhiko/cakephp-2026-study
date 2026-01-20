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
            'tags' => [
                '_ids' => ['1', '2'],
            ],
        ];

        // Act
        $this->post('/articles/add', $data);

        // Assert
        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Articles', 'action' => 'index']);
        $this->assertFlashMessage('The article has been saved.');

        // Verify the article was actually saved
        $Articles = $this->getTableLocator()->get('Articles');
        /** @var \App\Model\Entity\Article|null $article */
        $article = $Articles->find()
            ->contain(['Tags'])
            ->where(['slug' => 'new-test-article'])
            ->first();
        $this->assertNotNull($article, 'Article should be saved');

        // Verify tags are associated
        $this->assertNotEmpty($article->tags, 'Article should have related tags');
        $this->assertCount(2, $article->tags, 'Article should have 2 tags');
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
            'tags' => [
                '_ids' => ['2', '3'],
            ],
        ];

        // Act
        $this->post('/articles/edit/1', $data);

        // Assert
        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Articles', 'action' => 'index']);
        $this->assertFlashMessage('The article has been saved.');

        // Verify the article was actually updated
        $Articles = $this->getTableLocator()->get('Articles');
        $article = $Articles->get(1, contain: ['Tags']);
        $this->assertEquals('Updated Article Title', $article->title);

        // Verify tags are updated
        $this->assertNotEmpty($article->tags, 'Article should have related tags');
        $this->assertCount(2, $article->tags, 'Article should have 2 tags');
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
        $Articles = $this->getTableLocator()->get('Articles');
        $query = $Articles->find()->where(['id' => 1]);
        $this->assertEquals(0, $query->count());
    }
}
