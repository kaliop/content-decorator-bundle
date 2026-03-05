<?php

declare(strict_types=1);

namespace Kaliop\Bundle\ContentDecorator\DependencyInjection;

final class ServiceTags
{
    public const CONTENT_MAPPER = 'kaliop.content_decorator.content_mapper';
    public const CONTENT_DECORATOR_INJECTOR = 'kaliop.content_decorator.injector';
    public const CONTENT_REPOSITORY = 'kaliop.content_decorator.repository';
}
