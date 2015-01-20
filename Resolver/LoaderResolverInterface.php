<?php

/*
 * This File is part of the Thapp\JitImage\Resolver package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\JitImage\Resolver;

use Thapp\JitImage\Loader\LoaderInterface;

/**
 * @interface LoaderResolverInterface
 *
 * @package Thapp\JitImage\Resolver
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface LoaderResolverInterface extends ResolverInterface
{
    public function add($prefix, LoaderInterface $loader);
}