<?php

declare(strict_types=1);

namespace Kaliop\Bundle\ContentDecorator;

use Doctrine\DBAL\Types\Type;
use Kaliop\Bundle\ContentDecorator\Doctrine\Type\DecoratedContentListType;
use Kaliop\Bundle\ContentDecorator\Doctrine\Type\DecoratedContentType;
use Kaliop\Contracts\ContentDecorator\ContentDecoratorManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class KaliopContentDecoratorBundle extends Bundle
{
    public function __construct()
    {
        if (!Type::hasType(DecoratedContentType::TYPE)) {
            Type::addType(DecoratedContentType::TYPE, DecoratedContentType::class);
        }
        if (!Type::hasType(DecoratedContentListType::TYPE)) {
            Type::addType(DecoratedContentListType::TYPE, DecoratedContentListType::class);
        }
    }

    public function boot(): void
    {
        parent::boot();

        /** @var ContentDecoratorManager $contentManager */
        $contentManager = $this->container?->get(ContentDecoratorManager::class);

        /** @var LoggerInterface $logger */
        $logger = $this->container?->get('logger', ContainerInterface::NULL_ON_INVALID_REFERENCE);

        /** @var DecoratedContentType $decoratedContentType */
        $decoratedContentType = Type::getType(DecoratedContentType::TYPE);
        $decoratedContentType->setContentManager($contentManager);
        $decoratedContentType->setLogger($logger);

        /** @var DecoratedContentListType $decoratedContentType */
        $decoratedContentListType = Type::getType(DecoratedContentListType::TYPE);
        $decoratedContentListType->setContentManager($contentManager);
        $decoratedContentListType->setLogger($logger);
    }
}
