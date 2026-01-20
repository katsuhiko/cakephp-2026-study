<?php
declare(strict_types=1);

namespace CmsCore\Domain\Repository;

use CmsCore\Domain\Model\Article;
use CmsCore\Domain\Model\ArticleId;

/**
 * Article Repository Interface
 */
interface ArticleRepositoryInterface
{
    /**
     * Save article
     *
     * @param \CmsCore\Domain\Model\Article $article Article entity
     * @return \CmsCore\Domain\Model\Article Saved article with ID
     * @throws \RuntimeException When save fails
     */
    public function save(Article $article): Article;

    /**
     * Find article by ID
     *
     * @param \CmsCore\Domain\Model\ArticleId $id Article ID
     * @return \CmsCore\Domain\Model\Article|null
     */
    public function findById(ArticleId $id): ?Article;

    /**
     * Delete article
     *
     * @param \CmsCore\Domain\Model\ArticleId $id Article ID
     * @return bool
     */
    public function delete(ArticleId $id): bool;
}
