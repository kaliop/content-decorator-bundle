<?php

declare(strict_types=1);

namespace Kaliop\Contracts\ContentDecorator\Injector;

use Kaliop\Contracts\ContentDecorator\Model\ContentDecorator;

interface InjectorInterface
{
    /**
     * Injects extra data or services to a given content decorator.
     *
     * @param ContentDecorator $contentDecorator
     */
    public function inject(ContentDecorator $contentDecorator): void;

    /**
     * Check if given content decorator is supported by this injector.
     *
     * @param ContentDecorator $contentDecorator
     */
    public function supports(ContentDecorator $contentDecorator): bool;
}
