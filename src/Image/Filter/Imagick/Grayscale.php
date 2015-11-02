<?php

/*
 * This File is part of the Thapp\Jmg\Image\Filter\Imagick package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Jmg\Image\Filter\Imagick;

use Thapp\Jmg\ProcessorInterface;
use Thapp\Image\Filter\Imagick\Grayscale as ImageGrayscale;

/**
 * @class Grayscale
 *
 * @package Thapp\Jmg\Image\Filter\Imagick
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Grayscale extends AbstractImagickFilter
{
    public function apply(ProcessorInterface $proc, array $options = [])
    {
        $proc->getDriver()->filter(new ImageGrayscale);
    }
}
