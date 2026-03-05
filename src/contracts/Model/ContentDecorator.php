<?php

declare(strict_types=1);

namespace Kaliop\Contracts\ContentDecorator\Model;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Stringable;

abstract class ContentDecorator implements Stringable
{
    private Content $content;

    private ?Location $location;

    final public function __construct(
        Content $content,
        ?Location $location
    ) {
        $this->content = $content;
        $this->location = $location;
    }

    /**
     * Returns currently loaded content location if available, null otherwise.
     */
    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function getId(): int
    {
        return $this->getContent()->getId();
    }

    public function getVersionNo(): int
    {
        return $this->getContent()->getVersionInfo()->getVersionNo();
    }

    public function getContent(): Content
    {
        return $this->content;
    }

    public function isHidden(): bool
    {
        return $this->getContent()->getContentInfo()->isHidden();
    }

    public function isDraft(): bool
    {
        return $this->getContent()->getVersionInfo()->isDraft();
    }

    public function __toString(): string
    {
        return (string)$this->getContent()->getName();
    }
}
