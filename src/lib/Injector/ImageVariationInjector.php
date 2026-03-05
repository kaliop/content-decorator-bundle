<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator\Injector;

use Ibexa\Contracts\Core\Variation\VariationHandler;
use Kaliop\Contracts\ContentDecorator\Injector\InjectorInterface;
use Kaliop\Contracts\ContentDecorator\Injector\Type\ImageVariationAwareInterface;
use Kaliop\Contracts\ContentDecorator\Model\ContentDecorator;

class ImageVariationInjector implements InjectorInterface
{
    public function __construct(
        private VariationHandler $variationHandler,
    ) {}

    public function inject(ContentDecorator $contentDecorator): void
    {
        if ($contentDecorator instanceof ImageVariationAwareInterface) {
            $contentDecorator->setVariationHandler($this->variationHandler);
        }
    }

    public function supports(ContentDecorator $contentDecorator): bool
    {
        return $contentDecorator instanceof ImageVariationAwareInterface;
    }
}
