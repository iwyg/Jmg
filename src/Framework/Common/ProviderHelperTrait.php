<?php

/*
 * This File is part of the Thapp\Jmg package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Jmg\Framework\Common;

/**
 * @trait ProviderHelperTrait
 *
 * @package Thapp\Jmg
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
trait ProviderHelperTrait
{
    /**
     * getImagineClass
     *
     * @param string $driver
     *
     * @return string
     */
    private function getImagineClass($driver)
    {
        switch ($driver) {
            case 'gd':
                return '\Imagine\Gd\Imagine';
            case 'imagick':
                return '\Imagine\Imagick\Imagine';
            case 'gmagick':
                return '\Imagine\Gmagick\Imagine';
            default:
                break;
        }

        throw new \InvalidArgumentException('Invalid driver "'. $driver .'".');
    }

    private function getSourceClass($driver)
    {
        switch ($driver) {
            case 'gd':
                return '\Thapp\Image\Driver\Gd\Source';
            case 'imagick':
                return '\Thapp\Image\Driver\Imagick\Source';
            //case 'gmagick':
                //return '\Thapp\Image\Driver\Gmagick\Source';
            case 'im':
                return '\Thapp\Image\Driver\Im\Source';
            default:
                break;
        }

        throw new \InvalidArgumentException('Invalid driver "'. $driver .'".');
    }

    /**
     * getPathRegexp
     *
     * @return array
     */
    private function getPathRegexp()
    {
        return [
            '/{params}/{source}/{filter}',
            '([5|6](\/\d+){1}|[0]|[1|4](\/\d+){2}|[2](\/\d+){3}|[3](\/\d+){3}\/?(([0-9A-Fa-f]{3}|[0-9A-Fa-f]{6})|\d+)?)',
            //'([5|6](\/\d+){1}|[0]|[1|4](\/\d+){2}|[2](\/\d+){3}|[3](\/\d+){3}\/?([0-9A-Fa-f]{3}|[0-9A-Fa-f]{6})?)',
            '((([^0-9A-Fa-f]{3}|[^0-9A-Fa-f]{6})?).*?.(?=(\/filter:.*)?))',
            '(filter:.([^\/])*)'
        ];
    }
}
