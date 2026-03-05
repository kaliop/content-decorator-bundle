<?php

declare(strict_types=1);

namespace Kaliop\Contracts\ContentDecorator\Injector\Type;

use Symfony\Contracts\Translation\TranslatorInterface;

interface TranslatorAwareInterface
{
    public function setTranslator(TranslatorInterface $translator): void;
}
