<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator\Trait;

use Psr\Log\LoggerInterface;

trait LoggerAwareTrait
{
    protected LoggerInterface $logger;

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
