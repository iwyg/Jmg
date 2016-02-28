<?php

/*
 * This File is part of the Thapp\Jmg package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Jmg\Filter;

/**
 * @class ColorizeFilterTrait
 *
 * @package Thapp\Jmg
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
trait ColorizeFilterTrait
{
    /**
     * {@inheritdoc}
     */
    protected function parseOption($option, $value)
    {
        return str_pad(dechex($value), 6, '0', STR_PAD_LEFT);
    }

    protected function getShortOpts()
    {
        return ['c' => 'color'];
    }
}
