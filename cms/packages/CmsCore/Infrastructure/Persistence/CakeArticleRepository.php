<?php
declare(strict_types=1);

namespace CmsCore\Infrastructure\Persistence;

use App\Model\Entity\Tag;
use Cake\ORM\Locator\LocatorAwareTrait;
use CmsCore\Domain\Model\Article;
use CmsCore\Domain\Model\ArticleId;
use CmsCore\Domain\Repository\ArticleRepositoryInterface;
use RuntimeException;

/**
 * CakePHP Article Repository
 */
final class CakeArticleRepository implements ArticleRepositoryInterface
{
    use LocatorAwareTrait;

    /**
     * Save article
     *
     * @param \CmsCore\Domain\Model\Article $article Article entity
     * @return \CmsCore\Domain\Model\Article Saved article with ID
     * @throws \RuntimeException When save fails
     */
    public function save(Article $article): Article
    {
        /** @var \App\Model\Table\ArticlesTable $Articles */
        $Articles = $this->fetchTable('Articles');

        // Convert domain model to Cake entity
        if ($article->id === null) {
            // Create new
            $entity = $Articles->newEmptyEntity();
        } else {
            // Update existing
            $entity = $Articles->get($article->id->value, contain: ['Tags']);
        }

        // Set data
        $entity->user_id = $article->userId;
        $entity->title = $article->title;
        $entity->slug = $article->slug;
        $entity->body = $article->body;
        $entity->published = $article->published;

        $entity->tags = array_map(fn($id) => new Tag(['id' => $id]), $article->tagIds);

        // Save
        $saved = $Articles->save($entity);
        if ($saved === false) {
            throw new RuntimeException('Failed to save article');
        }

        // Convert back to domain model
        return Article::reconstruct([
            'id' => $saved->id,
            'user_id' => $saved->user_id,
            'title' => $saved->title,
            'slug' => $saved->slug,
            'body' => $saved->body,
            'published' => $saved->published,
            'tag_ids' => isset($saved->tags) ? array_map(fn(Tag $tag): int => (int)$tag->id, $saved->tags) : [],
            'created' => $saved->created->format('Y-m-d H:i:s'),
            'modified' => $saved->modified->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Find article by ID
     *
     * @param \CmsCore\Domain\Model\ArticleId $id Article ID
     * @return \CmsCore\Domain\Model\Article|null
     */
    public function findById(ArticleId $id): ?Article
    {
        /** @var \App\Model\Table\ArticlesTable $Articles */
        $Articles = $this->fetchTable('Articles');

        /** @var \App\Model\Entity\Article $entity */
        $entity = $Articles->find()
            ->where(['Articles.id' => $id->value])
            ->contain(['Tags'])
            ->first();

        if ($entity === null) {
            return null;
        }

        return Article::reconstruct([
            'id' => $entity->id,
            'user_id' => $entity->user_id,
            'title' => $entity->title,
            'slug' => $entity->slug,
            'body' => $entity->body,
            'published' => $entity->published,
            'tag_ids' => isset($entity->tags) ? array_map(fn(Tag $tag): int => (int)$tag->id, $entity->tags) : [],
            'created' => $entity->created->format('Y-m-d H:i:s'),
            'modified' => $entity->modified->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Delete article
     *
     * @param \CmsCore\Domain\Model\ArticleId $id Article ID
     * @return bool
     */
    public function delete(ArticleId $id): bool
    {
        /** @var \App\Model\Table\ArticlesTable $Articles */
        $Articles = $this->fetchTable('Articles');

        $entity = $Articles->get($id->value);

        return (bool)$Articles->delete($entity);
    }
}
