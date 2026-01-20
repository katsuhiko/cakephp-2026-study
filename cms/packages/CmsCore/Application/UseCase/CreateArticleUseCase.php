<?php
declare(strict_types=1);

namespace CmsCore\Application\UseCase;

use CmsCore\Domain\Model\Article;
use CmsCore\Domain\Repository\ArticleRepositoryInterface;
use DomainException;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * Create Article UseCase
 */
final readonly class CreateArticleUseCase
{
    /**
     * Constructor
     *
     * @param \CmsCore\Domain\Repository\ArticleRepositoryInterface $articleRepository Article repository
     * @param \Psr\Log\LoggerInterface $logger Logger
     */
    public function __construct(
        private ArticleRepositoryInterface $articleRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Execute
     *
     * @param array{
     *     user_id: int,
     *     title: string,
     *     slug: string,
     *     body: string,
     *     published: bool,
     *     tag_ids: array<int>
     * } $input Input data
     * @return array{success: bool, articleId: int|null, errors: array<string>} Result
     */
    public function execute(array $input): array
    {
        try {
            // Create domain model
            $article = Article::create($input);

            // Save via repository
            $savedArticle = $this->articleRepository->save($article);

            // Log success
            $this->logger->info('Article created successfully', [
                'article_id' => $savedArticle->id?->value,
                'user_id' => $input['user_id'] ?? null,
            ]);

            return [
                'success' => true,
                'articleId' => $savedArticle->id?->value,
                'errors' => [],
            ];
        } catch (DomainException $e) {
            // Domain validation error
            $this->logger->warning('Article creation failed: domain validation error', [
                'error' => $e->getMessage(),
                'input' => $input,
            ]);

            return [
                'success' => false,
                'articleId' => null,
                'errors' => [$e->getMessage()],
            ];
        } catch (Exception $e) {
            // Unexpected error
            $this->logger->error('Article creation failed: unexpected error', [
                'error' => $e->getMessage(),
                'input' => $input,
            ]);

            return [
                'success' => false,
                'articleId' => null,
                'errors' => ['An unexpected error occurred'],
            ];
        }
    }
}
