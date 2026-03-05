<?php

declare(strict_types=1);

namespace Kaliop\Bundle\ContentDecorator;

use Doctrine\DBAL\Types\Type;
use Kaliop\Bundle\ContentDecorator\Doctrine\Type\DecoratedContentType;
use Kaliop\Contracts\ContentDecorator\ContentDecoratorManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class KaliopContentDecoratorBundle extends Bundle
{
    public function __construct()
    {
        if (!Type::hasType(DecoratedContentType::TYPE)) {
            Type::addType(DecoratedContentType::TYPE, DecoratedContentType::class);
        }
    }

    public function boot(): void
    {
        parent::boot();

        /** @var \Kaliop\Contracts\ContentDecorator\ContentDecoratorManager $contentManager */
        $contentManager = $this->container?->get(ContentDecoratorManager::class);

        /** @var \Psr\Log\LoggerInterface $logger */
        $logger = $this->container?->get('logger', ContainerInterface::NULL_ON_INVALID_REFERENCE);

        /** @var \Kaliop\Bundle\ContentDecorator\Doctrine\Type\DecoratedContentType $decoratedContentType */
        $decoratedContentType = Type::getType(DecoratedContentType::TYPE);
        $decoratedContentType->setContentManager($contentManager);
        $decoratedContentType->setLogger($logger);
    }
}
