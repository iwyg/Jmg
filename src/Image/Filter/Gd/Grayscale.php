<?php

/*
 * This File is part of the Thapp\Jmg\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Jmg\Image\Filter\Gd;

use Thapp\Jmg\ProcessorInterface;
use Thapp\Image\Filter\Gd\Grayscale as GdGrayscale;

/**
 * @class Grayscale
 *
 * @package Thapp\Jmg
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Grayscale extends AbstractGdFilter
{
    public function apply(ProcessorInterface $proc, array $options = [])
    {
        $proc->getDriver()->filter(new GdGrayscale);
    }
}
