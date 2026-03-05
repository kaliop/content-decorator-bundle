<?php

declare(strict_types=1);

namespace Kaliop\Contracts\ContentDecorator\Injector\Type;

use Ibexa\Contracts\Core\Variation\VariationHandler;

interface ImageVariationAwareInterface
{
    public function setVariationHandler(VariationHandler $imageVariationService): void;
}
