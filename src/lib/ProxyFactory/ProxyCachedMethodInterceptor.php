<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator\ProxyFactory;

use Kaliop\Contracts\ContentDecorator\Model\ContentDecorator;
use ProxyManager\Proxy\AccessInterceptorInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @template T of ContentDecorator
 */
class ProxyCachedMethodInterceptor
{
    public function __construct(
        private readonly AdapterInterface $cache,
    ) {}

    /**
     * @param AccessInterceptorInterface<T>&T $proxy
     * @param T $realInstance
     * @param string $method
     * @param array<string, mixed> $params
     * @param bool $returnEarly
     *
     * @return mixed
     */
    public function prefix(
        AccessInterceptorInterface&ContentDecorator $proxy,
        ContentDecorator $realInstance,
        string $method,
        array $params,
        bool &$returnEarly
    ): mixed {
        $item = $this->cache->getItem($this->getCacheKey($method, $params));
        if ($item->isHit()) {
            $returnEarly = true;

            return $item->get();
        }

        $returnEarly = false;

        return null;
    }

    /**
     * @param AccessInterceptorInterface<T>&T $proxy
     * @param T $realInstance
     * @param string $method
     * @param array<string, mixed> $params
     * @param mixed $returnValue
     * @param bool $returnEarly
     *
     * @return mixed
     */
    public function suffix(
        AccessInterceptorInterface&ContentDecorator $proxy,
        ContentDecorator $realInstance,
        string $method,
        array $params,
        mixed $returnValue,
        bool &$returnEarly
    ): mixed {
        $item = $this->cache->getItem($this->getCacheKey($method, $params));
        $item->set($returnValue);

        $this->cache->save($item);

        return $returnValue;
    }

    /**
     * @param array<string, mixed> $params
     */
    private function getCacheKey(
        string $method,
        array $params
    ): string {
        return sha1($method . serialize($params));
    }
}
