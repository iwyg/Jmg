<?php

/**
 * This File is part of the Thapp\Jmg package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Jmg\Cache\Client;

use Thapp\Jmg\Cache\CacheInterface;

/**
 * @class ClientInterface
 *
 * @package Thapp\Jmg
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ClientInterface
{
    /**
     * Gets an item by key from the store.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key);

    /**
     * Saves an item to the store.
     *
     * @param stirng $key
     * @param mixed $content
     * @param int $expires lifetime in minutes
     *
     * @return void
     */
    public function set($key, $content, $expires = CacheInterface::EXPIRY_NONE);

    /**
     * Item exists.
     *
     * @param string $key
     *
     * @return boolean
     */
    public function has($key);

    /**
     * Deletes an item from the store.
     *
     * @param string $key
     *
     * @return boolean
     */
    public function delete($key);

    /**
     * Deletes multiple items from the store.
     *
     * @param array $keys
     *
     * @return boolean
     */
    public function deleteKeys(array $keys);
}
