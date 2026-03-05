<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator\Injector;

use Kaliop\Contracts\ContentDecorator\Injector\InjectorInterface;
use Kaliop\Contracts\ContentDecorator\Injector\Type\LoggerAwareInterface;
use Kaliop\Contracts\ContentDecorator\Model\ContentDecorator;
use Psr\Log\LoggerInterface;

class LoggerInjector implements InjectorInterface
{
    public function __construct(
        private LoggerInterface $logger,
    ) {}

    public function inject(ContentDecorator $contentDecorator): void
    {
        if ($contentDecorator instanceof LoggerAwareInterface) {
            $contentDecorator->setLogger($this->logger);
        }
    }

    public function supports(ContentDecorator $contentDecorator): bool
    {
        return $contentDecorator instanceof LoggerAwareInterface;
    }
}
