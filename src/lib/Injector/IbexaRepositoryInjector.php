<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator\Injector;

use Ibexa\Contracts\Core\Repository\Repository;
use Kaliop\Contracts\ContentDecorator\Injector\InjectorInterface;
use Kaliop\Contracts\ContentDecorator\Injector\Type\IbexaRepositoryAwareInterface;
use Kaliop\Contracts\ContentDecorator\Model\ContentDecorator;

class IbexaRepositoryInjector implements InjectorInterface
{
    public function __construct(
        private Repository $repository,
    ) {
    }

    public function inject(ContentDecorator $contentDecorator): void
    {
        if ($contentDecorator instanceof IbexaRepositoryAwareInterface) {
            $contentDecorator->setRepository($this->repository);
        }
    }

    public function supports(ContentDecorator $contentDecorator): bool
    {
        return $contentDecorator instanceof IbexaRepositoryAwareInterface;
    }
}
