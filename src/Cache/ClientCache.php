<?php

/**
 * This File is part of the Thapp\Jmg package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Jmg\Cache;

use Thapp\Jmg\ProcessorInterface;
use Thapp\Jmg\Resource\CacheClientResource;
use Thapp\Jmg\Cache\Client\ClientInterface;

/**
 * @class ClientCache
 *
 * @package Thapp\Jmg
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class ClientCache extends AbstractCache
{
    protected $id;
    protected $changes;
    protected $client;
    protected $resources;
    protected $pool;
    protected $resolved;

    /**
     * Constructor.
     *
     * @param MemcachedClient $client
     * @param string $id
     * @param int $expires
     */
    public function __construct(ClientInterface $client, $id = 'jitimage', $expires = self::EXPIRY_NONE)
    {
        $this->changes = [];
        $this->resolved = [];

        $this->client = $client;

        $this->id = $id;

        $this->initialize();
        $this->setExpires($expires);
    }

    /**
     * __destruct
     *
     * @return void
     */
    public function __destruct()
    {
        $this->flushCache();
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        list ($prefix, $id) = $this->getKeyAndPrefix($key);

        if (isset($this->pool[$prefix][$id]) && $this->client->has($this->getPrefixed($key))) {
            return true;
        }

        $this->changes(true);

        unset($this->pool[$prefix][$id]);

        if (empty($this->pool[$prefix])) {
            unset($this->pool[$prefix]);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $raw = self::CONTENT_RESOURCE)
    {
        if (!$this->has($key)) {
            return;
        }

        list ($prefix, $id) = $this->getKeyAndPrefix($key);

        $resource = $this->pool[$prefix][$id];

        if ($raw) {
            return $resource->getContents();
        }

        $resource->setClient($this->client);

        return $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, ProcessorInterface $proc)
    {
        list ($prefix, $id) = $this->getKeyAndPrefix($key);

        $this->pool[$prefix][$id] = $this->createResource($proc, $key, $id, $prefix);
    }

    /**
     * {@inheritdoc}
     */
    public function purge()
    {
        $keys = [];

        foreach ($this->pool as $prefix => $items) {
            $keys += $this->collectKeys($items);
        }

        if (!$this->client->deleteKeys($keys)) {
            return false;
        }

        $this->changes(!empty($keys));

        unset($this->pool);
        $this->pool = [];

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($file, $prefix = null)
    {
        list ($prefix, $id) = $this->getKeyAndPrefix($key = $this->createKey($file, $prefix));

        if (!isset($this->pool[$prefix])) {
            return;
        }

        $keys = $this->collectKeys($this->pool[$prefix]);

        if (!$this->client->deleteKeys($keys)) {
            return false;
        }

        $this->changes(true);

        unset($this->pool[$prefix]);

        return true;
    }

    /**
     * Creates the resource.
     *
     * @param ProcessorInterface $proc
     * @param string             $key
     * @param string             $id
     * @param string             $prefix
     *
     * @return CacheClientResource
     */
    private function createResource($proc, $key, $id, $prefix = '')
    {
        $resource = new CacheClientResource($proc, $key, $prefix . '/'. $id . '.' . $proc->getFileFormat());
        $resource->setClient($this->client);
        $this->client->set($this->getPrefixed($key), $proc->getContents(), $this->expires);

        return $resource;
    }

    /**
     * collectKeys
     *
     * @param array $pool
     *
     * @return array
     */
    private function collectKeys(array $pool)
    {
        return array_map(function (CachedResourceInterface $resource) {
            return $this->getPrefixed($resource->getKey());
        }, $pool);
    }

    /**
     * getKeyAndPrefix
     *
     * @param string $key
     *
     * @return string
     */
    private function getKeyAndPrefix($key)
    {
        if (!isset($this->resolved[$key])) {
            $this->resolved[$key] = array_pad(explode('.', $key, 2), -2, null);
        }

        return $this->resolved[$key];
    }

    /**
     * initialize
     *
     * @return void
     */
    private function initialize()
    {
        $this->pool = $this->client->get($this->indexKey()) ?: [];
    }

    /**
     * flushCache
     *
     *
     * @return void
     */
    private function flushCache()
    {
        if (empty($this->changes)) {
            return;
        }

        $this->client->set($this->indexKey(), $this->pool, $this->expires);
    }

    /**
     * getPrefixed
     *
     * @param string $key
     *
     * @return string
     */
    private function getPrefixed($key)
    {
        return $this->id .'_'. $key;
    }

    /**
     * indexKey
     *
     * @return string
     */
    public function indexKey()
    {
        return 'index.'.$this->id.hash('md5', $this->id);
    }

    /**
     * changes
     *
     * @param mixed $changed
     *
     * @return void
     */
    private function changes($changed)
    {
        if (false !== (bool)$changed) {
            $this->changes[] = true;
        }
    }
}
