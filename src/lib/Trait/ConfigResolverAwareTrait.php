<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator\Trait;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;

trait ConfigResolverAwareTrait
{
    protected ConfigResolverInterface $configResolver;

    public function setConfigResolver(ConfigResolverInterface $configResolver): void
    {
        $this->configResolver = $configResolver;
    }
}
