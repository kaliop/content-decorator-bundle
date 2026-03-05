<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator\Trait;

use Ibexa\Core\IO\IOServiceInterface;

trait IOServiceAwareTrait
{
    protected IOServiceInterface $ioService;

    public function setIOService(IOServiceInterface $ioService): void
    {
        $this->ioService = $ioService;
    }
}
