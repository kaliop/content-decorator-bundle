<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator\Injector;

use Kaliop\Contracts\ContentDecorator\Injector\InjectorInterface;
use Kaliop\Contracts\ContentDecorator\Injector\Type\TranslatorAwareInterface;
use Kaliop\Contracts\ContentDecorator\Model\ContentDecorator;
use Symfony\Contracts\Translation\TranslatorInterface;

class TranslatorInjector implements InjectorInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {}

    public function inject(ContentDecorator $contentDecorator): void
    {
        if ($contentDecorator instanceof TranslatorAwareInterface) {
            $contentDecorator->setTranslator($this->translator);
        }
    }

    public function supports(ContentDecorator $contentDecorator): bool
    {
        return $contentDecorator instanceof TranslatorAwareInterface;
    }
}
