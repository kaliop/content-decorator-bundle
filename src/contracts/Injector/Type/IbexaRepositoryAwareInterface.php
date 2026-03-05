<?php

declare(strict_types=1);

namespace Kaliop\Contracts\ContentDecorator\Injector\Type;

use Ibexa\Contracts\Core\Repository\Repository;

interface IbexaRepositoryAwareInterface
{
    public function setRepository(Repository $repository): void;
}
