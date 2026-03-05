<?php

declare(strict_types=1);

namespace Kaliop\Contracts\ContentDecorator\Injector\Type;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;

interface ConfigResolverAwareInterface
{
    public function setConfigResolver(ConfigResolverInterface $configResolver): void;
}
