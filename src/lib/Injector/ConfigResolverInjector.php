<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator\Injector;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Kaliop\Contracts\ContentDecorator\Injector\InjectorInterface;
use Kaliop\Contracts\ContentDecorator\Injector\Type\ConfigResolverAwareInterface;
use Kaliop\Contracts\ContentDecorator\Model\ContentDecorator;

class ConfigResolverInjector implements InjectorInterface
{
    public function __construct(
        private ConfigResolverInterface $configResolver,
    ) {}

    public function inject(ContentDecorator $contentDecorator): void
    {
        if ($contentDecorator instanceof ConfigResolverAwareInterface) {
            $contentDecorator->setConfigResolver($this->configResolver);
        }
    }

    public function supports(ContentDecorator $contentDecorator): bool
    {
        return $contentDecorator instanceof ConfigResolverAwareInterface;
    }
}
