<?php

/*
 * This File is part of the Thapp\Jmg package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Jmg\Framework\Common\Resolver;

use Thapp\Jmg\Resolver\CacheResolver;
use Thapp\Jmg\Framework\Common\Resolver\LazyResolverTrait;

/**
 * @class LazyCacheResolver
 *
 * @package Thapp\Jmg
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractLazyCacheResolver extends CacheResolver
{
    use LazyResolverTrait;

    protected $app;
    protected $settings;

    /**
     * {@inheritdoc}
     */
    public function resolve($alias)
    {
        if ($cache = parent::resolve($alias = trim($alias, '/'))) {
            return $cache;
        }

        if (null === $cache = $this->resolveCache($alias)) {
            return;
        }

        $this->add($alias, $cache);

        return $cache;
    }

    public function has($alias)
    {
        if (parent::has($alias)) {
            return true;
        }

        return (boolean)$this->resolveCache($alias);
    }

    /**
     * resolveCache
     *
     * @param string $alias
     *
     * @return Thapp\Jmg\Cache\CacheInterface `NULL` if none is found.
     */
    protected function resolveCache($alias)
    {
        if (null === $name = $this->getCacheName($alias)) {
            return;
        }

        if (method_exists($this, $method = 'create'.ucfirst($name).'Cache')) {
            return call_user_func([$this, $method]);
        } elseif ($this->hasCustomCreator($name)) {
            return $this->callCustomCreator($name, [$this->app]);
        }
    }


    /**
     * getCacheName
     *
     * @param string $alias
     *
     * @return string `NULL` if disabled
     */
    abstract protected function getCacheName($alias);

    /**
     * Get the caches config values.
     *
     * @return array
     */
    abstract protected function getCaches();

    /**
     * createFileCache
     *
     * @return void
     */
    protected function createFileCache()
    {
        $path =  $this->getDefaultCachePath();
        $expires = $this->getCacheExpiryTime();

        return new \Thapp\Jmg\Cache\FilesystemCache($path, null, $expires);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        foreach ($this->getAliases() as $alias) {
            $this->resolve($alias);
        }

        return parent::getIterator();
    }

    abstract protected function getAliases();

    /**
     * createHybridCache
     *
     * @return void
     */
    abstract protected function createHybridCache();

    /**
     * getDefaultCachePath
     *
     * @return void
     */
    abstract protected function getDefaultCachePath();
    abstract protected function getCacheExpiryTime();
}
