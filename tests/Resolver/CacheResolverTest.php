<?php

/*
 * This File is part of the Thapp\Jmg\Tests\Resolver package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Jmg\Tests\Resolver;

use Thapp\Jmg\Resolver\CacheResolver;

/**
 * @class CacheResolverTest
 *
 * @package Thapp\Jmg\Tests\Resolver
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class CacheResolverTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Thapp\Jmg\Resolver\CacheResolver', new CacheResolver);
    }

    /** @test */
    public function itShouldbeInitializableWithcaches()
    {
        $caches = [
            'foo' => $this->mockCache(),
            'bar' => $this->mockCache(),
        ];
        $resolver = new CacheResolver($caches);
        $this->assertTrue($resolver->has('foo'));
    }

    /** @test */
    public function itShouldBeIteratable()
    {
        $caches = [
            'foo' => $this->mockCache(),
            'bar' => $this->mockCache(),
        ];

        $resolver = new CacheResolver($caches);

        foreach ($resolver as $key => $cache) {
            $this->assertTrue(isset($caches[$key]));
            $this->assertSame($caches[$key], $cache);
        }
    }

    /** @test */
    public function itShouldResolverACacheByAlias()
    {
        $res = $this->newResolver();

        $this->assertNull($res->resolve('bar'));

        $res->add('bar', $cache = $this->mockCache());

        $this->assertSame($cache, $res->resolve('bar'));
    }

    /** @test */
    public function itShouldHaveCache()
    {
        $res = $this->newResolver();
        $this->assertFalse($res->has('bar'));
        $res->add('bar', $cache = $this->mockCache());
        $this->assertTrue($res->has('bar'));
    }

    /**
     * newResolver
     *
     * @return CacheResolver
     */
    protected function newResolver()
    {
        return new CacheResolver;
    }

    /**
     * mockCache
     *
     * @return Thapp\Jmg\Cache\CacheInterface
     */
    protected function mockCache()
    {
        return $this->getMock('Thapp\Jmg\Cache\CacheInterface');
    }
}
