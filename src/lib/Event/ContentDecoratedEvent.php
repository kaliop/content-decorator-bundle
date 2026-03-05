<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator\Event;

use Kaliop\Contracts\ContentDecorator\Model\ContentDecorator;
use Symfony\Contracts\EventDispatcher\Event;

class ContentDecoratedEvent extends Event
{
    public function __construct(
        private readonly ContentDecorator $contentDecorator,
    ) {
    }

    public function getContentDecorator(): ContentDecorator
    {
        return $this->contentDecorator;
    }
}
