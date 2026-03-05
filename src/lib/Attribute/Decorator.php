<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class Decorator
{
    /**
     * @var string[]
     */
    public array $contentTypes;

    /**
     * @var string|null
     */
    public ?string $repositoryClass;

    /**
     * @param string|null $repositoryClass
     * @param string[] $contentTypes
     */
    public function __construct(
        string|array $contentTypes = [],
        ?string $repositoryClass = null
    ) {
        $this->contentTypes = (array) $contentTypes;
        $this->repositoryClass = $repositoryClass;
    }
}
