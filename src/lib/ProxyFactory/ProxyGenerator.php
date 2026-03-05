<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator\ProxyFactory;

use Kaliop\ContentDecorator\Attribute\Cacheable;
use Kaliop\Contracts\ContentDecorator\Model\ContentDecorator;
use Laminas\Code\Generator\ClassGenerator;
use ProxyManager\Configuration as ProxyConfiguration;
use ProxyManager\Factory\AccessInterceptorValueHolderFactory;
use ProxyManager\FileLocator\FileLocator;
use ProxyManager\GeneratorStrategy\FileWriterGeneratorStrategy;
use ProxyManager\Proxy\AccessInterceptorInterface;
use ProxyManager\ProxyGenerator\AccessInterceptorValueHolderGenerator;
use ProxyManager\Version;
use ReflectionClass;
use RuntimeException;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

final class ProxyGenerator
{
    private ?ProxyConfiguration $config = null;

    public function __construct(
        private readonly string $proxyCacheDir,
        private readonly int $cacheLifetime = 0,
        private readonly int $maxCacheItems = 0,
    ) {
    }

    /**
     * @template T of ContentDecorator
     *
     * @param T $decorator
     *
     * @return AccessInterceptorInterface<T>
     */
    public function createProxy(ContentDecorator $decorator): AccessInterceptorInterface
    {
        $config = $this->getProxyConfiguration();
        $factory = new AccessInterceptorValueHolderFactory($config);

        $cache = $this->createCachePool();

        /** @var ProxyCachedMethodInterceptor<T> $interceptor */
        $interceptor = new ProxyCachedMethodInterceptor($cache);

        $prefix = [];
        $suffix = [];

        $cacheableMethods = $this->getCacheableMethods($decorator);
        foreach ($cacheableMethods as $name) {
            $prefix[$name] = $interceptor->prefix(...);
            $suffix[$name] = $interceptor->suffix(...);
        }

        // @phpstan-ignore-next-line
        return $factory->createProxy($decorator, $prefix, $suffix);
    }

    /**
     * Warmup the proxy cache by generating proxies for the given classes.
     *
     * @param iterable<string> $classNames
     */
    public function warmUp(iterable $classNames): void
    {
        // Proxy cache directory needs to be created before
        if (!is_dir($this->proxyCacheDir)) {
            if (false === @mkdir($this->proxyCacheDir, 0777, true)) {
                if (!is_dir($this->proxyCacheDir)) {
                    $error = error_get_last();

                    throw new RuntimeException(
                        sprintf(
                            'Unable to create the Repository Proxy directory "%s": %s',
                            $this->proxyCacheDir,
                            $error ? $error['message'] : 'unknown error',
                        )
                    );
                }
            }
        } elseif (!is_writable($this->proxyCacheDir)) {
            throw new RuntimeException(
                sprintf(
                    'The Repository Proxy directory "%s" is not writeable for the current system user.',
                    $this->proxyCacheDir
                )
            );
        }

        $config = new ProxyConfiguration();
        $config->setGeneratorStrategy(new FileWriterGeneratorStrategy(new FileLocator($this->proxyCacheDir)));
        $config->setProxiesTargetDir($this->proxyCacheDir);

        $generator = new AccessInterceptorValueHolderGenerator();
        foreach ($classNames as $className) {
            if (!class_exists($className)) {
                continue;
            }

            $ref = new ReflectionClass($className);
            if (!$ref->isSubclassOf(ContentDecorator::class) || $ref->isAbstract()) {
                continue;
            }

            $proxyParams = [
                'className' => $className,
                'factory' => AccessInterceptorValueHolderFactory::class,
                'proxyManagerVersion' => Version::getVersion(),
                'proxyOptions' => [],
            ];

            $proxyClassName = $config->getClassNameInflector()->getProxyClassName($className, $proxyParams);

            $proxyClass = new ClassGenerator($proxyClassName);
            $generator->generate($ref, $proxyClass);

            $signed = $config->getClassSignatureGenerator()->addSignature($proxyClass, $proxyParams);
            $config->getGeneratorStrategy()->generate($signed);
        }

        /** @var callable $autoloader */
        $autoloader = $config->getProxyAutoloader();
        spl_autoload_register($autoloader);
    }

    /**
     * @return string[]
     */
    private function getCacheableMethods(ContentDecorator $decorator): array
    {
        $cacheableMethods = [];

        $ref = new ReflectionClass($decorator);
        foreach ($ref->getMethods() as $method) {
            if ($method->isStatic() || $method->isConstructor() || $method->isDestructor()) {
                continue;
            }

            if ($method->getAttributes(Cacheable::class)) {
                $cacheableMethods[] = $method->getName();
            }
        }

        return $cacheableMethods;
    }

    private function getProxyConfiguration(): ProxyConfiguration
    {
        if ($this->config === null) {
            $this->config = new ProxyConfiguration();
            $this->config->setGeneratorStrategy(new FileWriterGeneratorStrategy(new FileLocator($this->proxyCacheDir)));
            $this->config->setProxiesTargetDir($this->proxyCacheDir);
        }

        return $this->config;
    }

    private function createCachePool(): AdapterInterface
    {
        return new ArrayAdapter(
            defaultLifetime: $this->cacheLifetime,
            storeSerialized: false,
            maxLifetime: $this->cacheLifetime,
            maxItems: $this->maxCacheItems,
        );
    }
}
