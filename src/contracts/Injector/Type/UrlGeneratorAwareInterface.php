<?php

declare(strict_types=1);

namespace Kaliop\Contracts\ContentDecorator\Injector\Type;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

interface UrlGeneratorAwareInterface
{
    public function setUrlGenerator(UrlGeneratorInterface $urlGenerator): void;
}
