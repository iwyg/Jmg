<?php

/**
 * This File is part of the Thapp\Jmg package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Jmg\Resolver;

use ArrayIterator;
use IteratorAggregate;
use Thapp\Jmg\Cache\CacheInterface;
use Thapp\Jmg\Resource\ImageResource;

/**
 * @class CacheResolver
 *
 * @package Thapp\Jmg
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class CacheResolver implements CacheResolverInterface, IteratorAggregate
{
    /**
     * caches
     *
     * @var array
     */
    protected $caches;

    /**
     * Constructor.
     *
     * @param array $caches
     */
    public function __construct(array $caches = [])
    {
        $this->set($caches);
    }

    /**
     * Add a cache instance to the resolver
     *
     * @param sting $alias
     * @param CacheInterface $cache
     *
     * @return void
     */
    public function add($alias, CacheInterface $cache)
    {
        $this->caches[$alias] = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function set(array $caches)
    {
        $this->caches = [];

        foreach ($caches as $alias => $cache) {
            $this->add($alias, $cache);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function has($alias)
    {
        return array_key_exists($alias, $this->caches);
    }

    /**
     * Resolves an alias to a cache instance
     *
     * @param string $alias
     *
     * @return null|CachInterface return null if no cache was found
     */
    public function resolve($alias)
    {
        if (array_key_exists($alias, $this->caches)) {
            return $this->caches[$alias];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->caches);
    }
}
