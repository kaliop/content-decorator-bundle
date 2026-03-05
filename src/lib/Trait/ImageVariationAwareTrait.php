<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator\Trait;

use Ibexa\Contracts\Core\Variation\VariationHandler;

trait ImageVariationAwareTrait
{
    protected VariationHandler $imageVariationService;

    public function setVariationHandler(VariationHandler $imageVariationService): void
    {
        $this->imageVariationService = $imageVariationService;
    }
}
