<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator\Trait;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

trait UrlGeneratorAwareTrait
{
    protected UrlGeneratorInterface $urlGenerator;

    public function setUrlGenerator(UrlGeneratorInterface $urlGenerator): void
    {
        $this->urlGenerator = $urlGenerator;
    }
}
