<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator\Event;

use Kaliop\Contracts\ContentDecorator\Model\ContentDecorator;
use Symfony\Contracts\EventDispatcher\Event;

class MultipleContentDecoratedEvent extends Event
{
    /**
     * @param ContentDecorator[] $contentDecorators
     */
    public function __construct(
        private readonly array $contentDecorators,
    ) {}

    /**
     * @return ContentDecorator[]
     */
    public function getContentDecorators(): array
    {
        return $this->contentDecorators;
    }
}
