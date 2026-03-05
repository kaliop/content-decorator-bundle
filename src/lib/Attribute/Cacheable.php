<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class Cacheable
{
}
