<?php

/*
 * This File is part of the Thapp\Jmg package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */


namespace Thapp\Jmg\Tests\Validator;

use Thapp\Jmg\Validator\ModeConstraints;

/**
 * @class ModeConstraintsTest
 * @see \PHPUnit_Framework_TestCase
 *
 * @package Thapp\Jmg
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ModeConstraintsTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Thapp\Jmg\Validator\ValidatorInterface', new ModeConstraints([]));
    }

    /**
     * @test
     * @dataProvider constraintsProvider
     */
    public function itShouldValidateConstraints($mode, $constraints, $parameters, $expected)
    {
        $validator = new ModeConstraints($constraints);

        $this->assertSame($expected, $validator->validate($mode, $parameters));
    }

    /** @test */
    public function itShouldReportTrueIfNoConstraints()
    {
        $validator = new ModeConstraints([]);

        $this->assertTrue($validator->validate(5, []));
    }

    public function constraintsProvider()
    {
        return [
            [1, [1 => [200, 200]], [100, 100], true],
            [0, [0 => [200, 200]], [500, 500], true],
            [4, [4 => [200, 200]], [500, 100], false],
            [5, [5 => [100]], [50], true],
            [5, [5 => [100]], [200], false],
        ];
    }
}
