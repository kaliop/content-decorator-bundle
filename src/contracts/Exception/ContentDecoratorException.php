<?php

declare(strict_types=1);

namespace Kaliop\Contracts\ContentDecorator\Exception;

use Exception;
use Throwable;

abstract class ContentDecoratorException extends Exception
{
    public function __construct(
        string $message,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            $message,
            0,
            $previous,
        );
    }
}
