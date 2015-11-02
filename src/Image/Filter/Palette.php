<?php

/*
 * This File is part of the Thapp\Jmg package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Jmg\Image\Filter;

use Thapp\Jmg\ProcessorInterface;
use Thapp\Image\Filter\Palette as PaletteFilter;

/**
 * @class Rotate
 *
 * @package Thapp\Jmg
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Palette extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    public function apply(ProcessorInterface $proc, array $options = [])
    {

        $this->setOptions($options);

        $image = $proc->getDriver();
        $image->filter(new PaletteFilter($this->getOption('p', PaletteFilter::PALETTE_RGB)));
    }

    protected function parseOption($option, $value)
    {
        switch ($value) {
        case 'rgb':
            return 0;
        case 'cmyk':
            return 1;
        case 'gray':
        case 'grayscale':
            return 2;
        }

        return min(2, max(0, (int)$value));
    }

    protected function getShortOpts()
    {
        return ['p' => 'palette'];
    }
}
