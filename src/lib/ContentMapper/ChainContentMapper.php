<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator\ContentMapper;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Kaliop\Contracts\ContentDecorator\ContentMapper\ContentMapperInterface;

class ChainContentMapper implements ContentMapperInterface
{
    /**
     * @param iterable<ContentMapperInterface> $contentMappers
     */
    public function __construct(
        private readonly iterable $contentMappers
    ) {
    }

    public function getClassName(Content $content): ?string
    {
        foreach ($this->contentMappers as $contentMapper) {
            $className = $contentMapper->getClassName($content);
            if ($className) {
                return $className;
            }
        }

        return null;
    }
}
