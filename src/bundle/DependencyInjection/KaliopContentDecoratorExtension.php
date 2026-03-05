<?php

declare(strict_types=1);

namespace Kaliop\Bundle\ContentDecorator\DependencyInjection;

use Kaliop\Bundle\ContentDecorator\Cache\Warmer\ProxyCacheWarmer;
use Kaliop\ContentDecorator\ContentMapper\AttributeContentMapper;
use Kaliop\ContentDecorator\ContentMapper\ChainContentMapper;
use Kaliop\ContentDecorator\ContentMapper\ContentTypeMapper;
use Kaliop\ContentDecorator\Factory\ContentDecoratorFactory;
use Kaliop\ContentDecorator\Factory\RepositoryFactory;
use Kaliop\ContentDecorator\Model\GenericContent;
use Kaliop\ContentDecorator\Repository\ContentRepository;
use Kaliop\Contracts\ContentDecorator\ContentMapper\ContentMapperInterface;
use Kaliop\Contracts\ContentDecorator\Injector\InjectorInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class KaliopContentDecoratorExtension extends Extension
{
    public function load(
        array $configs,
        ContainerBuilder $container
    ): void {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('parameters.yaml');
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $mappings = $config['mappings'] ?? [];
        $contentTypes = $config['content_types'] ?? [];
        $defaultClass = $config['default_class'] ?? GenericContent::class;
        $defaultRepositoryClass = $config['default_repository_class'] ?? ContentRepository::class;

        $container->setParameter('kaliop.content_decorator.default_class', $defaultClass);
        $container->setParameter('kaliop.content_decorator.default_repository_class', $defaultRepositoryClass);
        $container->setParameter('kaliop.content_decorator.mappings', $mappings);
        $container->setParameter('kaliop.content_decorator.content_types', $contentTypes);

        if ($contentTypes) {
            $definition = new Definition(ContentTypeMapper::class, [$contentTypes]);
            $definition->addTag(ServiceTags::CONTENT_MAPPER);

            $container->setDefinition(ContentTypeMapper::class, $definition);
        }

        if ($mappings) {
            foreach ($mappings as $name => $mapping) {
                $definition = new Definition(AttributeContentMapper::class, [$mapping['namespace'], $mapping['dir']]);
                $definition->addTag(ServiceTags::CONTENT_MAPPER);

                $container->setDefinition(sprintf('kaliop.content_decorator.attribute_mapper.%s', strtolower($name)), $definition);
            }
        }

        $definition = $container->getDefinition(ContentDecoratorFactory::class);
        $definition->setArgument(2, new Reference(ChainContentMapper::class));
        $definition->setArgument(4, $defaultClass);

        $definition = $container->getDefinition(RepositoryFactory::class);
        $definition->setArgument(3, $defaultRepositoryClass);

        $definition = $container->getDefinition(ProxyCacheWarmer::class);
        $definition->setArgument(1, $mappings);
        $definition->setArgument(2, $contentTypes);
        $definition->setArgument(3, $defaultClass);

        $this->registerForAutoConfiguration($container);
    }

    private function registerForAutoConfiguration(ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(ContentMapperInterface::class)
            ->addTag(ServiceTags::CONTENT_MAPPER);

        $container->registerForAutoconfiguration(InjectorInterface::class)
            ->addTag(ServiceTags::CONTENT_DECORATOR_INJECTOR);
    }
}
