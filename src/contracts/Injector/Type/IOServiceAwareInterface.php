<?php

declare(strict_types=1);

namespace Kaliop\Contracts\ContentDecorator\Injector\Type;

use Ibexa\Core\IO\IOServiceInterface;

interface IOServiceAwareInterface
{
    public function setIOService(IOServiceInterface $ioService): void;
}
