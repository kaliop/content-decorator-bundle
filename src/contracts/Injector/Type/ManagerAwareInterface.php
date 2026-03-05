<?php

declare(strict_types=1);

namespace Kaliop\Contracts\ContentDecorator\Injector\Type;

use Kaliop\Contracts\ContentDecorator\ContentDecoratorManager;

interface ManagerAwareInterface
{
    public function setContentDecoratorManager(ContentDecoratorManager $manager): void;
}
