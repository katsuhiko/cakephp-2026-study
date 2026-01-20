<?php
declare(strict_types=1);

namespace CmsCore\Domain\Model;

use InvalidArgumentException;

/**
 * Article ID Value Object
 */
final readonly class ArticleId
{
    /**
     * Constructor
     *
     * @param int $value ID value
     */
    private function __construct(
        public int $value,
    ) {
        if ($value <= 0) {
            throw new InvalidArgumentException('Article ID must be greater than 0');
        }
    }

    /**
     * Create from int
     *
     * @param int $value ID value
     * @return self
     */
    public static function fromInt(int $value): self
    {
        return new self($value);
    }

    /**
     * Equals comparison
     *
     * @param self $other Other ArticleId
     * @return bool
     */
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
