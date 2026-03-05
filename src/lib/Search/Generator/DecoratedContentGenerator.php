<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator\Search\Generator;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Kaliop\Contracts\ContentDecorator\ContentDecoratorManager;
use Kaliop\Contracts\ContentDecorator\Model\ContentDecorator;

/**
 * @template T of ContentDecorator
 */
class DecoratedContentGenerator
{
    /**
     * @var SearchResult|null
     */
    private ?SearchResult $searchResult = null;

    /**
     * @var int|null
     */
    private ?int $count = null;

    /**
     * @param array{languages: string[], useAlwaysAvailable: bool}|array{} $languageFilter
     */
    public function __construct(
        private Query $query,
        private readonly SearchService $searchService,
        private readonly ContentDecoratorManager $manager,
        private readonly array $languageFilter = [],
        private readonly bool $filterOnUserPermissions = true,
        int $batchSize = 50,
    ) {
        $query->offset = 0;
        $query->limit = $batchSize;
        $query->performCount = true;
    }

    /**
     * @return iterable<T>
     */
    public function getIterator(): iterable
    {
        if (!$this->searchResult) {
            $this->initialize();
        }

        $lastBatchIds = [];
        $lastTotalCount = $this->count();

        while (true) {
            if (!$this->searchResult || count($this->searchResult->searchHits) === 0) {
                break;
            }

            $currentBatchIds = [];
            foreach ($this->searchResult->searchHits as $searchHit) {
                /** @var Content|Location $object */
                $object = $searchHit->valueObject;
                if (!in_array($object->id, $lastBatchIds)) {
                    $currentBatchIds[] = $object->id;

                    /** @var T $decoratedContent */
                    $decoratedContent = $this->manager->decorate($object);

                    yield $decoratedContent;
                }
            }

            // Next batch
            $this->query->offset += $this->query->limit;
            $this->updateSearchResults();

            if ($this->searchResult?->totalCount !== $lastTotalCount) {
                // Total count has change while processing last batch
                // Decrease offset and rerun the query
                $this->query->offset -= $this->query->limit;
                $this->updateSearchResults();

                $lastTotalCount = $this->searchResult?->totalCount ?: 0;
            }

            $lastBatchIds = $currentBatchIds;
        }
    }

    /**
     * @return int
     */
    public function count(): int
    {
        if ($this->count === null) {
            $this->initialize();
        }

        return $this->count ?? 0;
    }

    /**
     * Initialize first batch of the results.
     */
    private function initialize(): void
    {
        $this->updateSearchResults();
        $this->count = $this->searchResult?->totalCount;
    }

    /**
     * Update search results by current $query object.
     */
    private function updateSearchResults(): void
    {
        if ($this->query instanceof LocationQuery) {
            $this->searchResult = $this->searchService->findLocations(
                $this->query,
                $this->languageFilter,
                $this->filterOnUserPermissions
            );
        } else {
            $this->searchResult = $this->searchService->findContent(
                $this->query,
                $this->languageFilter,
                $this->filterOnUserPermissions
            );
        }
    }
}
