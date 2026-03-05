<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator\ContentMapper;

use Kaliop\Contracts\ContentDecorator\ContentMapper\ContentMapperInterface;
use Kaliop\Contracts\ContentDecorator\Model\ContentDecorator;

class ContentTypeMapper extends AbstractContentIdentifierMapper implements ContentMapperInterface
{
    /**
     * @param array<string, class-string<ContentDecorator>> $mapping
     */
    public function __construct(
        private readonly array $mapping,
    ) {
    }

    public function getClassNameByIdentifier(string $identifier): ?string
    {
        return $this->mapping[$identifier] ?? null;
    }
}
