<?php

declare(strict_types=1);

namespace Kaliop\Contracts\ContentDecorator\Repository;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Kaliop\Contracts\ContentDecorator\Model\ContentDecorator;

/**
 * @template T of ContentDecorator
 *
 * @phpstan-import-type TSearchLanguageFilter from SearchService
 */
interface RepositoryInterface
{
    /**
     * Find a single content by a given criteria.
     *
     * @param Criterion[] $criteria
     * @param SortClause[] $sortClauses
     * @param bool $filterOnUserPermissions
     *
     * @phpstan-param TSearchLanguageFilter $languageFilter
     *
     * @return T|null
     */
    public function findOneBy(
        array $criteria = [],
        array $sortClauses = [],
        array $languageFilter = [],
        bool $filterOnUserPermissions = true,
    ): ?ContentDecorator;

    /**
     * Find a single location by a given criteria.
     *
     * @param Criterion[] $criteria
     * @param SortClause[] $sortClauses
     * @param bool $filterOnUserPermissions
     *
     * @phpstan-param TSearchLanguageFilter $languageFilter
     *
     * @return T|null
     */
    public function findOneLocationBy(
        array $criteria = [],
        array $sortClauses = [],
        array $languageFilter = [],
        bool $filterOnUserPermissions = true,
    ): ?ContentDecorator;

    /**
     * Find a list of contents by a given criteria.
     *
     * @param Criterion[] $criteria
     * @param SortClause[] $sortClauses
     * @param int|null $limit
     * @param int|null $offset
     * @param bool $filterOnUserPermissions
     *
     * @phpstan-param TSearchLanguageFilter $languageFilter
     *
     * @return T[]
     */
    public function findBy(
        array $criteria = [],
        array $sortClauses = [],
        ?int $limit = null,
        ?int $offset = null,
        array $languageFilter = [],
        bool $filterOnUserPermissions = true,
    ): array;

    /**
     * Find a list of locations by a given criteria.
     *
     * @param Criterion[] $criteria
     * @param SortClause[] $sortClauses
     * @param int|null $limit
     * @param int|null $offset
     * @param bool $filterOnUserPermissions
     *
     * @phpstan-param TSearchLanguageFilter $languageFilter
     *
     * @return T[]
     */
    public function findLocationsBy(
        array $criteria = [],
        array $sortClauses = [],
        ?int $limit = null,
        ?int $offset = null,
        array $languageFilter = [],
        bool $filterOnUserPermissions = true,
    ): array;

    /**
     * Find visible contents by a given parent location ID.
     *
     * @param int $parentLocationId
     * @param SortClause[] $sortClauses
     * @param int|null $limit
     * @param int $offset
     * @param bool $filterOnUserPermissions
     *
     * @phpstan-param TSearchLanguageFilter $languageFilter
     *
     * @return T[]
     */
    public function findVisibleByParentLocation(
        int $parentLocationId,
        array $sortClauses = [],
        ?int $limit = null,
        int $offset = 0,
        array $languageFilter = [],
        bool $filterOnUserPermissions = true,
    ): array;

    /**
     * Find visible contents by a given subtree.
     *
     * @param string $subtree
     * @param SortClause[] $sortClauses
     * @param int|null $limit
     * @param int $offset
     * @param bool $filterOnUserPermissions
     *
     * @phpstan-param TSearchLanguageFilter $languageFilter
     *
     * @return T[]
     */
    public function findVisibleBySubtree(
        string $subtree,
        array $sortClauses = [],
        ?int $limit = null,
        int $offset = 0,
        array $languageFilter = [],
        bool $filterOnUserPermissions = true,
    ): array;
}
