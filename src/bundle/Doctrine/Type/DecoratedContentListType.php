<?php

declare(strict_types=1);

namespace Kaliop\Bundle\ContentDecorator\Doctrine\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Kaliop\Contracts\ContentDecorator\ContentDecoratorManager;
use Kaliop\Contracts\ContentDecorator\Exception\ContentDecoratorException;
use Kaliop\Contracts\ContentDecorator\Model\ContentDecorator;
use Psr\Log\LoggerInterface;

class DecoratedContentListType extends Type
{
    public const TYPE = 'decorated_content_list';

    /**
     * @var ContentDecoratorManager
     */
    private ContentDecoratorManager $contentManager;

    /**
     * @var LoggerInterface|null
     */
    private ?LoggerInterface $logger;

    /**
     * @param ContentDecoratorManager $contentManager
     */
    public function setContentManager(ContentDecoratorManager $contentManager): void
    {
        $this->contentManager = $contentManager;
    }

    /**
     * @param LoggerInterface|null $logger
     */
    public function setLogger(?LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function convertToDatabaseValue(
        $value,
        AbstractPlatform $platform
    ) {
        if ($value instanceof Collection) {
            $ids = [];

            foreach ($value->getIterator() as $content) {
                if ($content instanceof ContentDecorator) {
                    $ids[] = $content->getContent()->getId();
                }
            }

            if ($ids) {
                return '|' . implode('|', $ids) . '|';
            }
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function convertToPHPValue(
        $value,
        AbstractPlatform $platform
    ) {
        $contents = [];

        $ids = array_map('intval', explode('|', trim($value, '|')));
        foreach ($ids as $id) {
            if (!$id) {
                continue;
            }

            try {
                $contents[] = $this->contentManager->loadContent($id);
            } catch (ContentDecoratorException $e) {
                $this->logger?->warning('Cannot load Decorated Content for Doctrine: ' . $e->getMessage());
            }
        }

        return new ArrayCollection($contents);
    }

    /**
     * {@inheritDoc}
     */
    public function getSQLDeclaration(
        array $column,
        AbstractPlatform $platform
    ): string {
        return $platform->getVarcharTypeDeclarationSQL($column);
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultLength(AbstractPlatform $platform): int
    {
        return $platform->getVarcharDefaultLength();
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return self::TYPE;
    }
}
