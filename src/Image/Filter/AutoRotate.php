<?php

/*
 * This File is part of the Thapp\Jmg\Image\Filter package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Jmg\Image\Filter;

use Thapp\Jmg\ProcessorInterface;
use Thapp\Image\Color\Hex;
use Thapp\Image\Filter\AutoRotate as ImageAutoRotate;

/**
 * @class Rotate
 *
 * @package Thapp\Jmg\Image\Filter
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Rotate extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    public function apply(ProcessorInterface $proc, array $options = [])
    {
        $proc->getDriver()->filter(new ImageAutoRotate);
    }
}
