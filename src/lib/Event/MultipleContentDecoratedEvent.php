<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator\Event;

use Symfony\Contracts\EventDispatcher\Event;

class MultipleContentDecoratedEvent extends Event
{
    /**
     * @param \Kaliop\Contracts\ContentDecorator\Model\ContentDecorator[] $contentDecorators
     */
    public function __construct(
        private readonly array $contentDecorators,
    ) {
    }

    /**
     * @return \Kaliop\Contracts\ContentDecorator\Model\ContentDecorator[]
     */
    public function getContentDecorators(): array
    {
        return $this->contentDecorators;
    }
}
