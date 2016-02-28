<?php

/**
 * This File is part of the Thapp\Jmg package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Cache;

use \Thapp\Image\ProcessorInterface;
use \Thapp\Image\Resource\NullResource;

/**
 * @class FilesystemCache implements CacheInterface
 * @see CacheInterface
 *
 * @package Thapp\Jmg
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class NullCache extends AbstractCache
{
    /**
     * {@inheritdoc}
     */
    public function set($id, ProcessorInterface $proc)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id, $raw = false)
    {
        return new NullResource;
    }

    /**
     * {@inheritdoc}
     */
    public function purge()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($file, $prefix = '')
    {
        return null;
    }
}
