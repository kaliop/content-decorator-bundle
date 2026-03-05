<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator\Trait;

use Ibexa\Contracts\Core\Repository\Repository;

trait IbexaRepositoryAwareTrait
{
    protected Repository $repository;

    public function setRepository(Repository $repository): void
    {
        $this->repository = $repository;
    }
}
