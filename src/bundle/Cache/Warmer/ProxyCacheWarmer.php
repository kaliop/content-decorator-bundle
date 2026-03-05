<?php

declare(strict_types=1);

namespace Kaliop\Bundle\ContentDecorator\Cache\Warmer;

use Kaliop\ContentDecorator\ProxyFactory\ProxyGenerator;
use Kaliop\Contracts\ContentDecorator\Model\ContentDecorator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

final class ProxyCacheWarmer implements CacheWarmerInterface
{
    /**
     * @param array<string, array{namespace?: string, dir?: string}> $mappings
     * @param array<string, class-string<ContentDecorator>> $explicitMapping
     * @param class-string<ContentDecorator>|null $defaultClass
     */
    public function __construct(
        private readonly ProxyGenerator $generator,
        private readonly array $mappings = [],
        private readonly array $explicitMapping = [],
        private readonly ?string $defaultClass = null,
    ) {}

    public function isOptional(): bool
    {
        return true;
    }

    public function warmUp(string $cacheDir): array
    {
        $this->generator->warmUp($this->getDecoratorClassNames());

        return [];
    }

    /**
     * @return string[]
     */
    private function getDecoratorClassNames(): array
    {
        $classNames = [
            ...array_values($this->explicitMapping),
        ];

        if ($this->defaultClass) {
            $classNames[] = $this->defaultClass;
        }

        foreach ($this->mappings as $mapping) {
            $namespace = $mapping['namespace'] ?? null;
            $dir = $mapping['dir'] ?? null;
            if (!$namespace || !$dir || !is_dir($dir)) {
                continue;
            }

            $finder = new Finder();
            $finder->files()->in($dir)->name('*.php');

            foreach ($finder as $file) {
                $relative = str_replace([$dir, '.php', '/'], ['', '', '\\'], (string)$file->getRealPath());
                $className = $namespace . '\\' . trim($relative, '\\');

                if (class_exists($className)) {
                    $classNames[] = $className;
                }
            }
        }

        return array_values(array_unique($classNames));
    }
}
