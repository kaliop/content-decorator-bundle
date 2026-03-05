<?php

declare(strict_types=1);

$config = \Ibexa\CodeStyle\PhpCsFixer\InternalConfigFactory::build();

return $config->setFinder(
    PhpCsFixer\Finder::create()
        ->in(__DIR__ . '/src')
        ->files()->name('*.php')
)->setRules(array_merge(
    $config->getRules(),
    [
        'header_comment' => false,
    ]
));
