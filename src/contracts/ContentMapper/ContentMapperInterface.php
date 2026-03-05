<?php

declare(strict_types=1);

namespace Kaliop\Contracts\ContentDecorator\ContentMapper;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Kaliop\Contracts\ContentDecorator\Model\ContentDecorator;

interface ContentMapperInterface
{
    /**
     * @return class-string<ContentDecorator>|null
     */
    public function getClassName(Content $content): ?string;
}
