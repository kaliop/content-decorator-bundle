<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator;

use Kaliop\Contracts\ContentDecorator\Injector\InjectorInterface;
use Kaliop\Contracts\ContentDecorator\Model\ContentDecorator;

class InjectorCollection
{
    /**
     * @param iterable<InjectorInterface> $injectors
     */
    public function __construct(
        private readonly iterable $injectors
    ) {
    }

    public function injectDependencies(ContentDecorator $contentDecorator): void
    {
        foreach ($this->injectors as $injector) {
            if ($injector->supports($contentDecorator)) {
                $injector->inject($contentDecorator);
            }
        }
    }
}
