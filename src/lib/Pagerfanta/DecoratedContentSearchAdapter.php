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
     * @param array{languages: string[], useAlwaysAvailable: bool}|array<void> $languageFilter
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
     * @param int $offset
     * @param int $length
     *
     * @return ContentDecorator[]
     *
     * @phpstan-ignore-next-line
     */
    public function getSlice(
        $offset,
        $length
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
     * @param array{languages: string[], useAlwaysAvailable: bool}|array<void> $languageFilter
     */
    protected function executeQuery(
        SearchService $searchService,
        Query $query,
        array $languageFilter
    ): SearchResult {
        if ($query instanceof LocationQuery) {
            return $searchService->findLocations($query, $languageFilter);
        } else {
            return $searchService->findContent($query, $languageFilter);
        }
    }
}
