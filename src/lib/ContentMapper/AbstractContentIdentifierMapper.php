<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator\ContentMapper;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Kaliop\Contracts\ContentDecorator\ContentMapper\ContentMapperInterface;
use Kaliop\Contracts\ContentDecorator\Model\ContentDecorator;

abstract class AbstractContentIdentifierMapper implements ContentMapperInterface
{
    final public function getClassName(Content $content): ?string
    {
        return $this->getClassNameByIdentifier($content->getContentType()->identifier);
    }

    /**
     * @return class-string<ContentDecorator>|null
     */
    abstract public function getClassNameByIdentifier(string $identifier): ?string;
}
