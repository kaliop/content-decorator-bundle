<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator\ContentMapper;

use Kaliop\ContentDecorator\Attribute\Decorator;
use Kaliop\Contracts\ContentDecorator\ContentMapper\ContentMapperInterface;
use Kaliop\Contracts\ContentDecorator\Model\ContentDecorator;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

class AttributeContentMapper extends AbstractContentIdentifierMapper implements ContentMapperInterface
{
    /**
     * @var array<class-string<ContentDecorator>, string[]>
     */
    private array $classes = [];

    public function __construct(
        string $namespace,
        string $directory
    ) {
        $finder = new Finder();
        $finder->files()->in($directory)->name('*.php');

        foreach ($finder as $file) {
            $relativePath = str_replace([$directory, '.php', '/'], ['', '', '\\'], $file->getRealPath());
            $className = $namespace . '\\' . trim($relativePath, '\\');

            if (class_exists($className)) {
                $class = new ReflectionClass($className);
                if (is_subclass_of($className, ContentDecorator::class)) {
                    foreach ($class->getAttributes(Decorator::class) as $attribute) {
                        $instance = $attribute->newInstance();
                        if ($instance instanceof Decorator && $instance->contentTypes) {
                            $this->classes[$className] = $instance->contentTypes;
                            break;
                        }
                    }
                }
            }
        }
    }

    public function getClassNameByIdentifier(string $identifier): ?string
    {
        foreach ($this->classes as $className => $contentTypes) {
            if (in_array($identifier, $contentTypes, true)) {
                return $className;
            }
        }

        return null;
    }
}
