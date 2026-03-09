<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator\Factory;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Kaliop\ContentDecorator\Event\ContentDecoratedEvent;
use Kaliop\ContentDecorator\Event\MultipleContentDecoratedEvent;
use Kaliop\ContentDecorator\Exception\ContentDecoratorMissingException;
use Kaliop\ContentDecorator\Exception\ContentDecoratorTrashedException;
use Kaliop\ContentDecorator\InjectorCollection;
use Kaliop\ContentDecorator\ProxyFactory\ProxyGenerator;
use Kaliop\Contracts\ContentDecorator\ContentMapper\ContentMapperInterface;
use Kaliop\Contracts\ContentDecorator\Exception\ContentDecoratorException;
use Kaliop\Contracts\ContentDecorator\Model\ContentDecorator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Service\ResetInterface;
use WeakReference;

/**
 * @internal
 */
class ContentDecoratorFactory implements ResetInterface
{
    /**
     * Cache of decorated content, using weak references to allow garbage collection when no longer in use.
     * The cache key is generated based on content ID, version number, and languages used in the content fields.
     * The value is an array of weak references to ContentDecorator instances, where each key is a unique content location (0 for content without location).
     *
     * @var array<string, array<int, WeakReference<ContentDecorator>>>
     */
    private array $cache = [];

    /**
     * @param class-string<ContentDecorator>|null $defaultClass
     */
    public function __construct(
        private readonly ProxyGenerator $proxyFactory,
        private readonly InjectorCollection $injector,
        private readonly ContentMapperInterface $contentMapper,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ?string $defaultClass,
    ) {}

    /**
     * @param Content $content
     * @param Location|null $location
     *
     * @return ContentDecorator
     *
     * @throws ContentDecoratorException
     */
    public function decorate(
        Content $content,
        ?Location $location
    ): ContentDecorator {
        $contentDecorator = $this->loadCached($content, $location);
        if ($contentDecorator) {
            return $contentDecorator;
        }

        return $this->refresh($content, $location);
    }

    /**
     * @param Content $content
     * @param Location|null $location
     *
     * @return ContentDecorator
     *
     * @throws ContentDecoratorException
     */
    public function refresh(
        Content $content,
        ?Location $location
    ): ContentDecorator {
        $contentDecorator = $this->initialize($content, $location);

        $event = new ContentDecoratedEvent($contentDecorator);
        $this->eventDispatcher->dispatch($event);

        $key = $this->getCacheKey($content);
        $this->cache[$key][$location?->id ?? 0] = WeakReference::create($contentDecorator);

        return $contentDecorator;
    }

    /**
     * @param iterable<Content|Location> $objects
     *
     * @return ContentDecorator[]
     *
     * @throws ContentDecoratorException
     */
    public function decorateMultiple(iterable $objects): array
    {
        $contentDecorators = [];
        $initializedContents = [];

        foreach ($objects as $object) {
            if ($object instanceof Content) {
                $content = $object;
                $location = $content->getContentInfo()->getMainLocation();
            } elseif ($object instanceof Location) {
                $location = $object;
                $content = $location->getContent();
            } else {
                throw new \InvalidArgumentException(sprintf('Object of type %s is not supported for decoration.', get_debug_type($object)));
            }

            $contentDecorator = $this->loadCached($content, $location);
            if ($contentDecorator) {
                $contentDecorators[] = $contentDecorator;
                continue;
            }

            $contentDecorator = $this->initialize($content, $location);
            $initializedContents[] = $contentDecorator;

            $key = $this->getCacheKey($content);
            $this->cache[$key][$location?->id ?? 0] = WeakReference::create($contentDecorator);

            $contentDecorators[] = $contentDecorator;
        }

        $event = new MultipleContentDecoratedEvent($initializedContents);
        $this->eventDispatcher->dispatch($event);

        return $contentDecorators;
    }

    /**
     * @throws ContentDecoratorMissingException
     * @throws ContentDecoratorTrashedException
     */
    private function initialize(
        Content $content,
        ?Location $location
    ): ContentDecorator {
        // Check if content is trashed
        if ($content->getContentInfo()->isTrashed()) {
            throw new ContentDecoratorTrashedException(sprintf('"%s" (id: %d) is trashed.', $content->getName(), $content->getId()));
        }

        // Decorate new content
        $className = $this->contentMapper->getClassName($content) ?? $this->defaultClass;
        if (!$className) {
            throw new ContentDecoratorMissingException(sprintf('Missing content decorator class for "%s" (id: %d).', $content->getName(), $content->getId()));
        }

        /** @var ContentDecorator $contentDecorator */
        $contentDecorator = new $className($content, $location ?? $content->getContentInfo()->getMainLocation());
        $this->injector->injectDependencies($contentDecorator);

        /** @var ContentDecorator $contentDecorator */
        $contentDecorator = $this->proxyFactory->createProxy($contentDecorator);

        return $contentDecorator;
    }

    private function loadCached(
        Content $content,
        ?Location $location
    ): ?ContentDecorator {
        $key = $this->getCacheKey($content);
        if (isset($this->cache[$key])) {
            if (!$location) {
                // Load with any location if not specified
                $locationId = array_key_first($this->cache[$key]);
                if ($locationId !== null && isset($this->cache[$key][$locationId])) {
                    /** @var ContentDecorator|null $contentDecorator */
                    $contentDecorator = $this->cache[$key][$locationId]->get();

                    return $contentDecorator;
                }
            } elseif (isset($this->cache[$key][$location->id ?? 0])) {
                /** @var ContentDecorator|null $contentDecorator */
                $contentDecorator = $this->cache[$key][$location->id ?? 0]->get();

                return $contentDecorator;
            }
        }

        return null;
    }

    private function getCacheKey(Content $content): string
    {
        $languages = [];

        foreach ($content->getFields() as $field) {
            if ($field->getLanguageCode() && !in_array($field->getLanguageCode(), $languages, true)) {
                $languages[] = $field->getLanguageCode();
            }
        }

        sort($languages);

        return sprintf('%d-%d-%s', $content->getId(), $content->getVersionInfo()->versionNo, implode(',', $languages));
    }

    public function reset(): void
    {
        $this->cache = [];
    }
}
