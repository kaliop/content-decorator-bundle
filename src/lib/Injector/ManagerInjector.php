<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator\Injector;

use Kaliop\Contracts\ContentDecorator\ContentDecoratorManager;
use Kaliop\Contracts\ContentDecorator\Injector\InjectorInterface;
use Kaliop\Contracts\ContentDecorator\Injector\Type\ManagerAwareInterface;
use Kaliop\Contracts\ContentDecorator\Model\ContentDecorator;

class ManagerInjector implements InjectorInterface
{
    public function __construct(
        private ContentDecoratorManager $manager,
    ) {
    }

    public function inject(ContentDecorator $contentDecorator): void
    {
        if ($contentDecorator instanceof ManagerAwareInterface) {
            $contentDecorator->setContentDecoratorManager($this->manager);
        }
    }

    public function supports(ContentDecorator $contentDecorator): bool
    {
        return $contentDecorator instanceof ManagerAwareInterface;
    }
}
