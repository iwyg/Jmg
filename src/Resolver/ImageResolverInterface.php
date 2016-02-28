<?php

/*
 * This File is part of the Thapp\Jmg package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Jmg\Resolver;

use Thapp\Jmg\Parameters;
use Thapp\Jmg\FilterExpression;

/**
 * @interface ImageResolverInterface
 *
 * @package Thapp\Jmg
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ImageResolverInterface
{
    /**
     * resolve
     *
     * @param Parameters $parameters
     * @param FilterExpression $filters
     *
     * @return void
     */
    public function resolve($src, Parameters $params, FilterExpression $filters = null, $prefix = '');

    /**
     * resolverCache
     *
     * @param array $parameters
     *
     * @return void
     */
    public function resolveCached($prefix, $key);

    /**
     * Get the image processor.
     *
     * @return ImageProcessorInterface
     */
    public function getProcessor();

    /**
     * Get the path resolver.
     *
     * @return ResolverInterface
     */
    public function getPathResolver();

    /**
     * Get the cache resolver
     *
     * @return CacheResolverInterface
     */
    public function getCacheResolver();

    /**
     * Get the Loader resolver.
     *
     * @return LoaderResolverInterface
     */
    public function getLoaderResolver();
}