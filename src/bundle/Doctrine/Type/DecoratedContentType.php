<?php

declare(strict_types=1);

namespace Kaliop\Bundle\ContentDecorator\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Kaliop\Contracts\ContentDecorator\ContentDecoratorManager;
use Kaliop\Contracts\ContentDecorator\Exception\ContentDecoratorException;
use Kaliop\Contracts\ContentDecorator\Model\ContentDecorator;
use Psr\Log\LoggerInterface;

class DecoratedContentType extends Type
{
    public const TYPE = 'decorated_content';

    /**
     * @var \Kaliop\Contracts\ContentDecorator\ContentDecoratorManager
     */
    private ContentDecoratorManager $contentManager;

    /**
     * @var \Psr\Log\LoggerInterface|null
     */
    private ?LoggerInterface $logger;

    /**
     * @param \Kaliop\Contracts\ContentDecorator\ContentDecoratorManager $contentManager
     */
    public function setContentManager(ContentDecoratorManager $contentManager): void
    {
        $this->contentManager = $contentManager;
    }

    /**
     * @param \Psr\Log\LoggerInterface|null $logger
     */
    public function setLogger(?LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof ContentDecorator) {
            return $value->getContent()->id;
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value) {
            try {
                return $this->contentManager->loadContent($value);
            } catch (ContentDecoratorException $e) {
                $this->logger?->warning('Cannot load Decorated Content for Doctrine: ' . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * @param mixed[] $column
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getIntegerTypeDeclarationSQL($column);
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return self::TYPE;
    }
}
