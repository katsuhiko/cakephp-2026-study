<?php
declare(strict_types=1);

namespace CmsCore\Domain\Model;

use DateTimeImmutable;
use DomainException;

/**
 * Article Entity
 */
final readonly class Article
{
    /**
     * Constructor
     *
     * @param \CmsCore\Domain\Model\ArticleId|null $id Article ID
     * @param int $userId User ID
     * @param string $title Title
     * @param string $slug Slug
     * @param string $body Body
     * @param bool $published Published flag
     * @param array<int> $tagIds Tag IDs
     * @param \DateTimeImmutable|null $created Created datetime
     * @param \DateTimeImmutable|null $modified Modified datetime
     */
    private function __construct(
        public ?ArticleId $id,
        public int $userId,
        public string $title,
        public string $slug,
        public string $body,
        public bool $published,
        public array $tagIds,
        public ?DateTimeImmutable $created,
        public ?DateTimeImmutable $modified,
    ) {
        $this->validateTitle($title);
        $this->validateSlug($slug);
        $this->validateBody($body);
    }

    /**
     * Create new Article
     *
     * @param array{
     *     user_id: int,
     *     title: string,
     *     slug: string,
     *     body: string,
     *     published: bool,
     *     tag_ids: array<int>
     * } $data Article data
     * @return self
     */
    public static function create(array $data): self
    {
        return new self(
            id: null,
            userId: (int)($data['user_id'] ?? 0),
            title: (string)($data['title'] ?? ''),
            slug: (string)($data['slug'] ?? ''),
            body: (string)($data['body'] ?? ''),
            published: (bool)($data['published'] ?? false),
            tagIds: isset($data['tag_ids']) && is_array($data['tag_ids'])
                ? array_map(fn(mixed $id): int => (int)$id, $data['tag_ids'])
                : [],
            created: null,
            modified: null,
        );
    }

    /**
     * Reconstruct from repository
     *
     * @param array{
     *     id?: int,
     *     user_id: int,
     *     title: string,
     *     slug: string,
     *     body: string,
     *     published: bool,
     *     tag_ids: array<int>,
     *     created?: string,
     *     modified?: string
     * } $data Article data
     * @return self
     */
    public static function reconstruct(array $data): self
    {
        return new self(
            id: isset($data['id']) ? ArticleId::fromInt((int)$data['id']) : null,
            userId: (int)($data['user_id'] ?? 0),
            title: (string)($data['title'] ?? ''),
            slug: (string)($data['slug'] ?? ''),
            body: (string)($data['body'] ?? ''),
            published: (bool)($data['published'] ?? false),
            tagIds: isset($data['tag_ids']) && is_array($data['tag_ids'])
                ? array_map('intval', $data['tag_ids'])
                : [],
            created: isset($data['created']) ? new DateTimeImmutable($data['created']) : null,
            modified: isset($data['modified']) ? new DateTimeImmutable($data['modified']) : null,
        );
    }

    /**
     * Update article
     *
     * @param array{
     *     user_id?: int,
     *     title?: string,
     *     slug?: string,
     *     body?: string,
     *     published?: bool,
     *     tag_ids?: array<int>
     * } $data Update data
     * @return self New instance with updated data
     */
    public function update(array $data): self
    {
        return new self(
            id: $this->id,
            userId: isset($data['user_id']) ? (int)$data['user_id'] : $this->userId,
            title: isset($data['title']) ? (string)$data['title'] : $this->title,
            slug: isset($data['slug']) ? (string)$data['slug'] : $this->slug,
            body: isset($data['body']) ? (string)$data['body'] : $this->body,
            published: isset($data['published']) ? (bool)$data['published'] : $this->published,
            tagIds: isset($data['tag_ids']) && is_array($data['tag_ids'])
                ? array_map('intval', $data['tag_ids'])
                : $this->tagIds,
            created: $this->created,
            modified: $this->modified,
        );
    }

    /**
     * Validate title
     *
     * @param string $title Title
     * @return void
     * @throws \DomainException
     */
    private function validateTitle(string $title): void
    {
        if (empty(trim($title))) {
            throw new DomainException('Title cannot be empty');
        }

        if (mb_strlen($title) > 255) {
            throw new DomainException('Title cannot exceed 255 characters');
        }
    }

    /**
     * Validate slug
     *
     * @param string $slug Slug
     * @return void
     * @throws \DomainException
     */
    private function validateSlug(string $slug): void
    {
        if (empty(trim($slug))) {
            throw new DomainException('Slug cannot be empty');
        }

        if (mb_strlen($slug) > 191) {
            throw new DomainException('Slug cannot exceed 191 characters');
        }

        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            throw new DomainException('Slug can only contain lowercase letters, numbers, and hyphens');
        }
    }

    /**
     * Validate body
     *
     * @param string $body Body
     * @return void
     * @throws \DomainException
     */
    private function validateBody(string $body): void
    {
        if (empty(trim($body))) {
            throw new DomainException('Body cannot be empty');
        }
    }
}
