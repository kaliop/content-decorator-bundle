<?php

declare(strict_types=1);

namespace Kaliop\Contracts\ContentDecorator\Repository;

use Kaliop\Contracts\ContentDecorator\Model\ContentDecorator;

/**
 * @template T of ContentDecorator
 */
interface RepositoryInterface
{
    /**
     * Find a single content by a given criteria.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion[] $criteria
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause[] $sortClauses
     *
     * @return T|null
     */
    public function findOneBy(array $criteria = [], array $sortClauses = []): ?ContentDecorator;

    /**
     * Find a single location by a given criteria.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion[] $criteria
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause[] $sortClauses
     *
     * @return T|null
     */
    public function findOneLocationBy(array $criteria = [], array $sortClauses = []): ?ContentDecorator;

    /**
     * Find a list of contents by a given criteria.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion[] $criteria
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause[] $sortClauses
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return T[]
     */
    public function findBy(array $criteria = [], array $sortClauses = [], ?int $limit = null, ?int $offset = null): array;

    /**
     * Find a list of locations by a given criteria.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion[] $criteria
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause[] $sortClauses
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return T[]
     */
    public function findLocationsBy(array $criteria = [], array $sortClauses = [], ?int $limit = null, ?int $offset = null): array;

    /**
     * Find visible contents by a given parent location ID.
     *
     * @param int $parentLocationId
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause[] $sortClauses
     * @param int|null $limit
     * @param int $offset
     *
     * @return T[]
     */
    public function findVisibleByParentLocation(int $parentLocationId, array $sortClauses = [], ?int $limit = null, int $offset = 0): array;

    /**
     * Find a visible contents by a given subtree.
     *
     * @param string $subtree
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause[] $sortClauses
     * @param int|null $limit
     * @param int $offset
     *
     * @return T[]
     */
    public function findVisibleBySubtree(string $subtree, array $sortClauses = [], ?int $limit = null, int $offset = 0): array;
}
