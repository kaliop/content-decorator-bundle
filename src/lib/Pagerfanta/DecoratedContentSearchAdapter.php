<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator\Pagerfanta;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Core\Pagination\Pagerfanta\AbstractSearchResultAdapter;
use Kaliop\Contracts\ContentDecorator\ContentDecoratorManager;
use Kaliop\Contracts\ContentDecorator\Model\ContentDecorator;

/**
 * Pagerfanta adapter for Ibexa content search.
 * Will return results as decorated content objects.
 *
 * @phpstan-import-type TSearchLanguageFilter from \Ibexa\Contracts\Core\Repository\SearchService
 *
 * @extends AbstractSearchResultAdapter<Content|Location>
 */
class DecoratedContentSearchAdapter extends AbstractSearchResultAdapter
{
    /**
     * @var ContentDecoratorManager
     */
    private ContentDecoratorManager $manager;

    /**
     * @param Query $query
     * @param SearchService $searchService
     * @param ContentDecoratorManager $manager
     *
     * @phpstan-param TSearchLanguageFilter $languageFilter
     */
    public function __construct(
        Query $query,
        SearchService $searchService,
        ContentDecoratorManager $manager,
        array $languageFilter = []
    ) {
        $this->manager = $manager;
        parent::__construct($query, $searchService, $languageFilter);
    }

    /**
     * @phpstan-param int<0, max> $offset
     * @phpstan-param int<0, max> $length
     *
     * @return ContentDecorator[]
     *
     * @phpstan-ignore-next-line
     */
    public function getSlice(
        int $offset,
        int $length
    ): array {
        $contents = [];

        foreach (parent::getSlice($offset, $length) as $hit) {
            if ($hit->valueObject instanceof Content) {
                $contents[] = $hit->valueObject;
            } elseif ($hit->valueObject instanceof Location) {
                $contents[] = $hit->valueObject->getContent();
            }
        }

        return $this->manager->decorateMultiple($contents);
    }

    /**
     * @phpstan-param TSearchLanguageFilter $languageFilter
     *
     * @return SearchResult<Content|Location>
     */
    protected function executeQuery(
        SearchService $searchService,
        Query $query,
        array $languageFilter
    ): SearchResult {
        if ($query instanceof LocationQuery) {
            /** @var SearchResult<Content|Location> $searchResult */
            $searchResult = $searchService->findLocations($query, $languageFilter);

            return $searchResult;
        }

        /** @var SearchResult<Content|Location> $searchResult */
        $searchResult = $searchService->findContent($query, $languageFilter);

        return $searchResult;
    }
}
