<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Kaliop\ContentDecorator\Search\Generator\DecoratedContentGenerator;
use Kaliop\Contracts\ContentDecorator\ContentDecoratorManager;
use Kaliop\Contracts\ContentDecorator\Model\ContentDecorator;
use Kaliop\Contracts\ContentDecorator\Repository\RepositoryInterface;

/**
 * @template T of ContentDecorator
 *
 * @implements RepositoryInterface<T>
 */
abstract class AbstractContentRepository implements RepositoryInterface
{
    public const SOLR_INT_MAX = 2147483647;

    /**
     * @param class-string<T> $className
     * @param string[] $contentTypes
     */
    public function __construct(
        protected readonly ContentDecoratorManager $manager,
        protected readonly Repository $repository,
        protected readonly ConfigResolverInterface $configResolver,
        protected readonly string $className,
        protected readonly array $contentTypes = [],
    ) {
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion[] $criteria
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause[] $sortClauses
     *
     * @return T|null
     */
    public function findOneBy(array $criteria = [], array $sortClauses = []): ?ContentDecorator
    {
        /** @var T[] $results */
        $results = $this->findBy($criteria, $sortClauses, 1);

        return $results[0] ?? null;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion[] $criteria
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause[] $sortClauses
     *
     * @return T|null
     */
    public function findOneLocationBy(array $criteria = [], array $sortClauses = []): ?ContentDecorator
    {
        /** @var T[] $results */
        $results = $this->findLocationsBy($criteria, $sortClauses, 1);

        return $results[0] ?? null;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion[] $criteria
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause[] $sortClauses
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return T[]
     */
    public function findBy(array $criteria = [], array $sortClauses = [], ?int $limit = null, ?int $offset = null): array
    {
        $query = new Query();

        $query->filter = $this->getFilters($criteria);
        $query->performCount = false;
        $query->limit = $limit ?: self::SOLR_INT_MAX - 1;
        $query->offset = $offset ?: 0;

        if ($sortClauses) {
            $query->sortClauses = $sortClauses;
        }

        return $this->findContents($query);
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion[] $criteria
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause[] $sortClauses
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return T[]
     */
    public function findLocationsBy(array $criteria = [], array $sortClauses = [], ?int $limit = null, ?int $offset = null): array
    {
        $query = new LocationQuery();

        $query->filter = $this->getFilters($criteria);
        $query->performCount = false;
        $query->limit = $limit ?: self::SOLR_INT_MAX - 1;
        $query->offset = $offset ?: 0;

        if ($sortClauses) {
            $query->sortClauses = $sortClauses;
        }

        return $this->findLocations($query);
    }

    /**
     * @param int $parentLocationId
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause[] $sortClauses
     * @param int|null $limit
     * @param int $offset
     *
     * @return T[]
     */
    public function findVisibleByParentLocation(int $parentLocationId, array $sortClauses = [], ?int $limit = null, int $offset = 0): array
    {
        if (!$sortClauses) {
            try {
                $parentLocation = $this->repository->getLocationService()->loadLocation($parentLocationId);
            } catch (NotFoundException|UnauthorizedException $e) {
                $parentLocation = null;
            }

            if ($parentLocation) {
                $sortClauses = $parentLocation->getSortClauses();
            }
        }

        return $this->findLocationsBy(
            [
                new Criterion\ParentLocationId($parentLocationId),
                new Criterion\Visibility(Criterion\Visibility::VISIBLE),
            ],
            $sortClauses,
            $limit,
            $offset,
        );
    }

    /**
     * @param string $subtree
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause[] $sortClauses
     * @param int|null $limit
     * @param int $offset
     *
     * @return T[]
     */
    public function findVisibleBySubtree(string $subtree, array $sortClauses = [], ?int $limit = null, int $offset = 0): array
    {
        return $this->findLocationsBy(
            [
                new Criterion\Subtree($subtree),
                new Criterion\Visibility(Criterion\Visibility::VISIBLE),
            ],
            $sortClauses,
            $limit,
            $offset,
        );
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion[] $criteria
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion
     */
    protected function getFilters(array $criteria = []): Criterion
    {
        $criteria[] = new Criterion\ContentTypeIdentifier($this->contentTypes);
        if (count($criteria) > 1) {
            return new Criterion\LogicalAnd($criteria);
        }

        return $criteria[0];
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery $query
     * @param array{languages: string[], useAlwaysAvailable: bool}|array<void> $languageFilter
     * @param bool $filterOnUserPermissions
     *
     * @return T[]
     */
    protected function findLocations(LocationQuery $query, array $languageFilter = [], bool $filterOnUserPermissions = true): array
    {
        return $this->decorateSearchResult(
            $this->repository->getSearchService()->findLocations($query, $languageFilter, $filterOnUserPermissions)
        );
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query $query
     * @param array{languages: string[], useAlwaysAvailable: bool}|array<void> $languageFilter
     * @param bool $filterOnUserPermissions
     *
     * @return T[]
     */
    protected function findContents(Query $query, array $languageFilter = [], bool $filterOnUserPermissions = true): array
    {
        return $this->decorateSearchResult(
            $this->repository->getSearchService()->findContent($query, $languageFilter, $filterOnUserPermissions)
        );
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult $searchResult
     *
     * @return T[]
     */
    protected function decorateSearchResult(SearchResult $searchResult): array
    {
        $contents = [];

        foreach ($searchResult->searchHits as $searchHit) {
            if ($searchHit->valueObject instanceof Content) {
                $contents[] = $searchHit->valueObject;
            } elseif ($searchHit->valueObject instanceof Location) {
                $contents[] = $searchHit->valueObject->getContent();
            }
        }

        /** @var T[] $results */
        $results = $this->manager->decorateMultiple($contents);

        return $results;
    }

    /**
     * @param array{languages: string[], useAlwaysAvailable: bool}|array{} $languageFilter
     *
     * @return DecoratedContentGenerator<T>
     */
    protected function createDecoratedContentGenerator(
        Query $query,
        array $languageFilter = [],
        bool $filterOnUserPermissions = true,
        int $batchSize = 50,
    ): DecoratedContentGenerator {
        /** @var DecoratedContentGenerator<T> $generator */
        $generator = new DecoratedContentGenerator(
            $query,
            $this->repository->getSearchService(),
            $this->manager,
            $languageFilter,
            $filterOnUserPermissions,
            $batchSize,
        );

        return $generator;
    }

    protected function getRootLocation(): Location
    {
        return $this->repository->getLocationService()->loadLocation(
            $this->configResolver->getParameter('content.tree_root.location_id')
        );
    }
}
