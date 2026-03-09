<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Kaliop\ContentDecorator\Exception\ContentDecoratorNotFoundException;
use Kaliop\ContentDecorator\Exception\ContentDecoratorUnauthorizedException;
use Kaliop\ContentDecorator\Factory\ContentDecoratorFactory;
use Kaliop\ContentDecorator\Factory\RepositoryFactory;
use Kaliop\Contracts\ContentDecorator\ContentDecoratorManager as ContentDecoratorManagerInterface;
use Kaliop\Contracts\ContentDecorator\Model\ContentDecorator;
use Kaliop\Contracts\ContentDecorator\Repository\RepositoryInterface;

class ContentDecoratorManager implements ContentDecoratorManagerInterface
{
    public function __construct(
        private readonly Repository $repository,
        private readonly ContentDecoratorFactory $decoratorFactory,
        private readonly RepositoryFactory $repositoryFactory,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function decorate(
        Content|Location $content,
        ?Location $location = null,
    ): ContentDecorator {
        if ($content instanceof Location) {
            $location = $content;
            $content = $content->getContent();
        } else {
            $location ??= $content->getContentInfo()->getMainLocation();
        }

        return $this->decoratorFactory->decorate($content, $location);
    }

    /**
     * {@inheritDoc}
     */
    public function decorateMultiple(array $objects): array
    {
        return $this->decoratorFactory->decorateMultiple($objects);
    }

    /**
     * {@inheritDoc}
     */
    public function loadContent(
        int $contentId,
        ?array $languages = null,
        ?int $versionNo = null,
        bool $useAlwaysAvailable = true
    ): ContentDecorator {
        try {
            return $this->decoratorFactory->decorate(
                $this->repository->getContentService()->loadContent($contentId, $languages, $versionNo, $useAlwaysAvailable),
                null,
            );
        } catch (NotFoundException $e) {
            throw new ContentDecoratorNotFoundException(sprintf('Content with ID %d not found.', $contentId), $e);
        } catch (UnauthorizedException $e) {
            throw new ContentDecoratorUnauthorizedException(sprintf('User does not have access to read content %d.', $contentId), $e);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function loadContentFromRemote(
        string $contentRemoteId,
        ?array $languages = null,
        ?int $versionNo = null,
        bool $useAlwaysAvailable = true
    ): ContentDecorator {
        try {
            return $this->decoratorFactory->decorate(
                $this->repository->getContentService()->loadContentByRemoteId($contentRemoteId, $languages, $versionNo, $useAlwaysAvailable),
                null,
            );
        } catch (NotFoundException $e) {
            throw new ContentDecoratorNotFoundException(sprintf('Content with remote ID %s not found.', $contentRemoteId), $e);
        } catch (UnauthorizedException $e) {
            throw new ContentDecoratorUnauthorizedException(sprintf('User does not have access to read content %s.', $contentRemoteId), $e);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function loadLocation(
        int $locationId,
        ?array $prioritizedLanguages = null,
        bool $useAlwaysAvailable = true
    ): ContentDecorator {
        try {
            $location = $this->repository->getLocationService()->loadLocation($locationId, $prioritizedLanguages, $useAlwaysAvailable);

            return $this->decoratorFactory->decorate(
                $location->getContent(),
                $location,
            );
        } catch (NotFoundException $e) {
            throw new ContentDecoratorNotFoundException(sprintf('Location with ID %d not found.', $locationId), $e);
        } catch (UnauthorizedException $e) {
            throw new ContentDecoratorUnauthorizedException(sprintf('User does not have access to read location %d.', $locationId), $e);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function loadLocationFromRemote(
        string $locationRemoteId,
        ?array $prioritizedLanguages = null,
        bool $useAlwaysAvailable = true
    ): ContentDecorator {
        try {
            $location = $this->repository->getLocationService()->loadLocationByRemoteId($locationRemoteId, $prioritizedLanguages, $useAlwaysAvailable);

            return $this->decoratorFactory->decorate(
                $location->getContent(),
                $location,
            );
        } catch (NotFoundException $e) {
            throw new ContentDecoratorNotFoundException(sprintf('Location with remote ID %s not found.', $locationRemoteId), $e);
        } catch (UnauthorizedException $e) {
            throw new ContentDecoratorUnauthorizedException(sprintf('User does not have access to read location %s.', $locationRemoteId), $e);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getRepository(string $className): RepositoryInterface
    {
        return $this->repositoryFactory->getRepository($this, $className);
    }
}
