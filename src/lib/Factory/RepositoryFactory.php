<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator\Factory;

use Ibexa\Contracts\Core\Repository\Repository as IbexaRepository;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Kaliop\ContentDecorator\Attribute\Decorator;
use Kaliop\ContentDecorator\Exception\InvalidContentDecoratorRepositoryException;
use Kaliop\ContentDecorator\Repository\AbstractContentRepository;
use Kaliop\Contracts\ContentDecorator\ContentDecoratorManager;
use Kaliop\Contracts\ContentDecorator\Model\ContentDecorator;
use Kaliop\Contracts\ContentDecorator\Repository\RepositoryInterface;
use ReflectionClass;

/**
 * @internal
 */
class RepositoryFactory
{
    /**
     * @var RepositoryInterface<ContentDecorator>[]
     */
    private array $repositories = [];

    /**
     * @param iterable<RepositoryInterface<ContentDecorator>> $serviceRepositories
     * @param class-string<AbstractContentRepository<ContentDecorator>>|null $defaultRepositoryClass
     */
    public function __construct(
        private readonly IbexaRepository $ibexaRepository,
        private readonly ConfigResolverInterface $configResolver,
        private readonly iterable $serviceRepositories,
        private readonly ?string $defaultRepositoryClass,
    ) {}

    /**
     * @template T of ContentDecorator
     *
     * @param class-string<T> $className
     *
     * @return RepositoryInterface<T>
     *
     * @throws InvalidContentDecoratorRepositoryException
     */
    public function getRepository(
        ContentDecoratorManager $manager,
        string $className
    ): RepositoryInterface {
        if (isset($this->repositories[$className])) {
            /** @var RepositoryInterface<T> $repository */
            $repository = $this->repositories[$className];

            return $repository;
        }

        if (!is_subclass_of($className, ContentDecorator::class)) {
            throw new InvalidContentDecoratorRepositoryException(sprintf('Class "%s" does not implement %s interface.', $className, ContentDecorator::class));
        }

        $class = new ReflectionClass($className);
        $this->repositories[$className] = $this->createRepository($class, $manager);

        return $this->repositories[$className];
    }

    /**
     * @template T of ContentDecorator
     *
     * @param ReflectionClass<T> $class
     *
     * @return RepositoryInterface<T>
     *
     * @throws InvalidContentDecoratorRepositoryException
     */
    protected function createRepository(
        ReflectionClass $class,
        ContentDecoratorManager $manager
    ): RepositoryInterface {
        $repositoryClass = null;
        $contentTypes = [];

        foreach ($class->getAttributes(Decorator::class) as $attribute) {
            $instance = $attribute->newInstance();
            if ($instance instanceof Decorator) {
                $repositoryClass = $instance->repositoryClass;
                $contentTypes = $instance->contentTypes;
                break;
            }
        }

        if ($repositoryClass) {
            if (!is_subclass_of($repositoryClass, RepositoryInterface::class)) {
                throw new InvalidContentDecoratorRepositoryException(sprintf('Repository "%s" does not implement %s interface.', $repositoryClass, RepositoryInterface::class));
            }

            foreach ($this->serviceRepositories as $repository) {
                if (get_class($repository) === $repositoryClass) {
                    /** @var RepositoryInterface<T> $repository */
                    return $repository;
                }
            }

            if (!is_subclass_of($repositoryClass, AbstractContentRepository::class)) {
                throw new InvalidContentDecoratorRepositoryException(sprintf('Repository "%s" does not implement %s abstraction.', $repositoryClass, AbstractContentRepository::class));
            }

            /** @var RepositoryInterface<T> $repository */
            $repository = new $repositoryClass($manager, $this->ibexaRepository, $this->configResolver, $class->getName(), $contentTypes);
        } else {
            $repositoryClass = $this->defaultRepositoryClass;
            if (!$repositoryClass || !is_subclass_of($repositoryClass, AbstractContentRepository::class)) {
                throw new InvalidContentDecoratorRepositoryException(sprintf('Default repository "%s" does not implement %s abstraction.', $repositoryClass, AbstractContentRepository::class));
            }

            /** @var RepositoryInterface<T> $repository */
            $repository = new $repositoryClass($manager, $this->ibexaRepository, $this->configResolver, $class->getName(), $contentTypes);
        }

        return $repository;
    }
}
