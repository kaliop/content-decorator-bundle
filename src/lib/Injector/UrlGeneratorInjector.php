<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator\Injector;

use Kaliop\Contracts\ContentDecorator\Injector\InjectorInterface;
use Kaliop\Contracts\ContentDecorator\Injector\Type\UrlGeneratorAwareInterface;
use Kaliop\Contracts\ContentDecorator\Model\ContentDecorator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UrlGeneratorInjector implements InjectorInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function inject(ContentDecorator $contentDecorator): void
    {
        if ($contentDecorator instanceof UrlGeneratorAwareInterface) {
            $contentDecorator->setUrlGenerator($this->urlGenerator);
        }
    }

    public function supports(ContentDecorator $contentDecorator): bool
    {
        return $contentDecorator instanceof UrlGeneratorAwareInterface;
    }
}
