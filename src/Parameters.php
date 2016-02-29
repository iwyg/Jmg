<?php

/**
 * This File is part of the Thapp\Jmg package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Jmg;

use Thapp\Image\Color\Parser;

/**
 * @class Parameters
 *
 * @package Thapp\Jmg
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Parameters
{
    /** @var string **/
    const P_SEPARATOR = '/';

    /** @var array **/
    private static $qMap = [
        'scale'      => ProcessorInterface::IM_RSIZEPERCENT,
        'pixel'      => ProcessorInterface::IM_RSIZEPXCOUNT,
        'scale-crop' => ProcessorInterface::IM_SCALECROP,
        'crop'       => ProcessorInterface::IM_CROP,
        'resize'     => ProcessorInterface::IM_RESIZE,
        'resize-fit' => ProcessorInterface::IM_RSIZEFIT
    ];

    /** @var string **/
    private $str;

    /** @var array **/
    private $params;

    /** @var string **/
    private $separator;

    /**
     * Constructor.
     *
     * @param array $params
     * @param string $separator
     */
    public function __construct(array $params = [], $separator = self::P_SEPARATOR)
    {
        $this->params = $params;
        $this->separator = $separator;
    }

    /**
     * @return void
     */
    public function __clone()
    {
        $this->str = null;
        $this->params = [];
    }

    /**
     * setHeight
     *
     * @param int $width
     * @param int $height
     *
     * @return void
     */
    public function setTargetSize($width = null, $height = null)
    {
        $this->str = null;
        $this->params['width']  = $width;
        $this->params['height'] = $height;
    }

    /**
     * setMode
     *
     * @param int $mode
     *
     * @return void
     */
    public function setMode($mode)
    {
        $this->str = null;
        $this->params['mode']  = (int)$mode;
    }

    /**
     * setGravity
     *
     * @param int $gravity
     *
     * @return void
     */
    public function setGravity($gravity = null)
    {
        $this->str = null;
        $this->params['gravity'] = $gravity;
    }

    /**
     * setBackground
     *
     * @param string $color
     *
     * @return void
     */
    public function setBackground($background = null)
    {
        $this->str = null;

        if (null !== $background && $this->isColor($background)) {
            $this->params['background'] = $background;
        }
    }

    /**
     * all
     *
     * @return array
     */
    public function all()
    {
        $params = array_merge(static::defaults(), $this->params);

        return static::sanitize(
            $params['mode'],
            $params['width'],
            $params['height'],
            $params['gravity'],
            $params['background']
        );
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->asString();
    }

    /**
     * toQueryString
     *
     * @return string
     */
    public function toQueryString()
    {
        return '?' . http_build_query($this->all());
    }

    /**
     * asString
     *
     * @return string
     */
    public function asString()
    {
        if (null === $this->str) {
            $this->str = implode($this->separator, array_filter(array_values($this->all()), function ($val) {
                return null !== $val;
            }));
        }

        return $this->str;
    }

    /**
     * Returns a new instance from a string.
     *
     * @param string $str
     *
     * @return self
     */
    public function createFromString($str)
    {
        return new self(static::parseString($str, $this->separator));
    }

    /**
     * parseString
     *
     * @param string $paramString
     * @param string $separator
     *
     * @return array
     */
    public static function parseString($paramString, $separator = self::P_SEPARATOR)
    {
        $parts = array_pad(explode($separator, $paramString), 5, null);

        if (isset($parts[4]) && (!is_numeric($parts[0]) && !static::isHex($parts[4]))) {
            $parts[4] = null;
        }

        list($mode, $width, $height, $gravity, $background) = array_map(function ($value, $key = null) {
            return is_numeric($value) ? (int)$value : ltrim($value, ' #');
        }, $parts);

        return static::sanitize($mode, $width, $height, $gravity, $background);
    }

    private static function parseBackground($background)
    {
        //$alpha = null;
        //if (is_string($background) && (5 === $len = strlen($alpha) || 8 === $len)) {
            //$alpha = substr($background, 0, 2);
            //$background = substr($background, 2);
        //}
    }

    /**
     * isColor
     *
     * @param mixed $color
     *
     * @return bool
     */
    protected function isColor($color)
    {
        return static::isHex($color);
    }

    /**
     * isHex
     *
     * @param mixed $color
     *
     * @return boolean
     */
    private static function isHex($color)
    {
        $color = ltrim($color, '#');

        return Parser::isHex($color);
    }

    /**
     * sanitize
     *
     * @param int $mode
     * @param int $width
     * @param int $height
     * @param int $gravity
     * @param string $background
     *
     * @access private
     * @return array
     */
    private static function sanitize($mode = null, $width = null, $height = null, $gravity = null, $background = null)
    {
        //$mode = max(0, min(6, (int)$mode));
        $mode = max(0, min(6, (int)$mode));
        $values = ['mode' => $mode];

        if (2 == $mode || 3 === $mode) {
            $values['gravity'] = $gravity ? (int)$gravity : 5;
        }

        if (3 === $mode && null !== $background) {
            $values['background'] = (is_int($background) && !Parser::isHex((string)$background)) ?
                $background : hexdec(Parser::normalizeHex($background));
        }

        switch ($mode) {
            case 1:
            case 2:
            case 3:
            case 4:
                $values['width'] = (int)$width;
                $values['height'] = (int)$height;
                break;
            case 5:
                $values['width'] = (float)$width;
                break;
            case 6:
                $values['width'] = (int)$width;
                break;
        }

        return array_merge(static::defaults(), $values);
    }

    /**
     * defaults
     *
     * @return array
     */
    private static function defaults()
    {
        return ['mode' => null, 'width' => null, 'height' => null, 'gravity' => null, 'background' => null];
    }

    /**
     * fromString
     *
     * @param mixed $paramString
     * @param mixed $separator
     *
     * @access public
     * @return Parameters
     */
    public static function fromString($paramString, $separator = self::P_SEPARATOR)
    {
        return new static(static::parseString($paramString, $separator), $separator);
    }

    public static function fromQuery(array $query)
    {
        $query = static::mapMode($query);


        $params = array_merge($default = static::defaults(), $query);

        if (null === $params['mode']) {
            $params['mode'] = 0;
        }

        extract(array_intersect_key($params, $default));

        return new static(static::sanitize($mode, $width, $height, $gravity, $background));
    }

    private static function mapMode(array $query)
    {
        $map = [
            'mode'=> isset($query['mode']) ? (int)$query['mode'] : ProcessorInterface::IM_NOSCALE,
        ];

        if (empty($query)) {
            return $map;
        }


        $map = array_merge($map, static::$qMap);
        $type = array_reduce(array_keys($query), function ($a, $b) use ($map) {
            return isset($map[$b]) ? $b :
                (isset($map[$a]) ? $a :  'mode');
        });

        $query['mode'] = $map[$type];

        return $query;
    }
}
