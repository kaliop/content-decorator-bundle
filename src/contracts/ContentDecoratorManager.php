<?php

declare(strict_types=1);

namespace Kaliop\Contracts\ContentDecorator;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Kaliop\Contracts\ContentDecorator\Exception\ContentDecoratorException;
use Kaliop\Contracts\ContentDecorator\Exception\InvalidContentDecoratorRepositoryException;
use Kaliop\Contracts\ContentDecorator\Model\ContentDecorator;
use Kaliop\Contracts\ContentDecorator\Repository\RepositoryInterface;

interface ContentDecoratorManager
{
    /**
     * Decorate given Content or Location.
     *
     * @param Content|Location $object
     *
     * @return ContentDecorator
     *
     * @throws ContentDecoratorException
     */
    public function decorate(Content|Location $object): ContentDecorator;

    /**
     * Decorate a given list of Content and/or Location objects.
     *
     * @param list<Content|Location> $objects
     *
     * @return array<ContentDecorator>
     *
     * @throws ContentDecoratorException
     */
    public function decorateMultiple(array $objects): array;

    /**
     * Loads and decorates content of a given ID.
     * Throws an exception if a current user is unauthorized or content is not found.
     *
     * @param int $contentId
     * @param string[]|null $languages
     * @param int|null $versionNo
     * @param bool $useAlwaysAvailable
     *
     * @return ContentDecorator
     *
     * @throws ContentDecoratorException
     */
    public function loadContent(
        int $contentId,
        ?array $languages = null,
        ?int $versionNo = null,
        bool $useAlwaysAvailable = true
    ): ContentDecorator;

    /**
     * Loads and decorates content of a given remote ID.
     * Throws an exception if a current user is unauthorized or content is not found.
     *
     * @param string $contentRemoteId
     * @param string[]|null $languages
     * @param int|null $versionNo
     * @param bool $useAlwaysAvailable
     *
     * @return ContentDecorator
     *
     * @throws ContentDecoratorException
     */
    public function loadContentFromRemote(
        string $contentRemoteId,
        ?array $languages = null,
        ?int $versionNo = null,
        bool $useAlwaysAvailable = true
    ): ContentDecorator;

    /**
     * Loads and decorates a location of a given ID.
     * Throws an exception if a current user is unauthorized or location is not found.
     *
     * @param int $locationId
     * @param string[]|null $prioritizedLanguages
     * @param bool $useAlwaysAvailable
     *
     * @return ContentDecorator
     *
     * @throws ContentDecoratorException
     */
    public function loadLocation(
        int $locationId,
        ?array $prioritizedLanguages = null,
        bool $useAlwaysAvailable = true
    ): ContentDecorator;

    /**
     * Loads and decorates a location of a given remote ID.
     * Throws an exception if a current user is unauthorized or a location is not found.
     *
     * @param string $locationRemoteId
     * @param string[]|null $prioritizedLanguages
     * @param bool $useAlwaysAvailable
     *
     * @return ContentDecorator
     *
     * @throws ContentDecoratorException
     */
    public function loadLocationFromRemote(
        string $locationRemoteId,
        ?array $prioritizedLanguages = null,
        bool $useAlwaysAvailable = true
    ): ContentDecorator;

    /**
     * Gets a repository for a given ContentDecorator class.
     * Throws an exception if a given class name is not any subclass of ContentDecorator.
     *
     * @template T of ContentDecorator
     *
     * @param class-string<T> $className
     *
     * @return RepositoryInterface<T>
     *
     * @throws InvalidContentDecoratorRepositoryException
     */
    public function getRepository(string $className): RepositoryInterface;
}
