<?php

/*
 * This File is part of the Thapp\Jmg\Tests\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Jmg\Tests\Http;

use Thapp\Jmg\Http\UrlSigner;

/**
 * @class UrlSignerTest
 *
 * @package Thapp\Jmg\Tests\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class UrlSignerTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Thapp\Jmg\Http\HttpSignerInterface', new UrlSigner('secret-key'));
    }

    /** @test */
    public function itShouldSignUrl()
    {
        $signer = new UrlSigner('my-key', 's');

        $signature = $signer->sign('/image/0/cat.jpg', $this->mockParameters());

        $this->assertTrue(0 === strpos($signature, '/image/0/cat.jpg?s='));
    }

    /** @test */
    public function itShouldValidateRequest()
    {
        $signer = new UrlSigner('my-key');
        $signature = $signer->sign($path = '/image/0/cat.jpg', $params = $this->mockParameters());

        $parts = parse_url($signature);
        parse_str($parts['query'], $q);

        $rq = $this->prepareRequest($path, $q['token']);

        $this->assertTrue($signer->validate($rq, $params));
    }

    /** @test */
    public function itShouldThrowIfTokenIsMissing()
    {
        $signer = new UrlSigner('my-key');
        $rq = $this->prepareRequest(null, null);

        try {
            $signer->validate($rq, $this->mockParameters());
        } catch (\Thapp\Jmg\Exception\InvalidSignatureException $e) {
            $this->assertSame($e->getMessage(), 'Signature is missing.');
        }
    }

    /** @test */
    public function itShouldThrowIfTokenIsInvalid()
    {
        $signer = new UrlSigner('my-key');

        $rq = $this->prepareRequest(null, 'invalidtoken');

        try {
            $signer->validate($rq, $this->mockParameters());
        } catch (\Thapp\Jmg\Exception\InvalidSignatureException $e) {
            $this->assertSame($e->getMessage(), 'Signature is invalid.');
        }
    }

    /**
     * prepareRequest
     *
     * @param mixed $path
     * @param mixed $query
     * @param string $key
     *
     * @return Symfony\Component\HttpFoundation\Request
     */
    protected function prepareRequest($path = null, $query = null, $key = 'token')
    {
        $q = $this->mockQuery();
        $q->method('get')->with($key)->willReturn($query);
        $rq = $this->mockRequest(['getPathInfo']);
        $rq->method('getQuery')->willReturn($q);

        $rq->method('getPathInfo')->willReturn($path);
        $rq->query = $q;

        return $rq;
    }

    /**
     * mockQuery
     *
     * @return Symfony\Component\HttpFoundation\ParameterBag
     */
    protected function mockQuery()
    {
        return $this->getMockBuilder('Symfony\Component\HttpFoundation\ParameterBag')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * mockParameters
     *
     * @param string $str
     *
     * @return Thapp\Jmg\Parameters;
     */
    protected function mockParameters($str = '0')
    {
        $mock = $this->getMockBuilder('Thapp\Jmg\Parameters')
            ->disableOriginalConstructor()
            ->getMock();
        $mock->method('asString')->willReturn($str);

        return $mock;
    }

    /**
     * mockRequest
     *
     * @param array $methods
     *
     * @return Symfony\Component\HttpFoundation\Request
     */
    protected function mockRequest(array $methods = [])
    {
        return $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->setMethods($methods)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
