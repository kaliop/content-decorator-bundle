<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator\Trait;

use Kaliop\Contracts\ContentDecorator\ContentDecoratorManager;

trait ManagerAwareTrait
{
    protected ContentDecoratorManager $manager;

    public function setContentDecoratorManager(ContentDecoratorManager $manager): void
    {
        $this->manager = $manager;
    }
}
