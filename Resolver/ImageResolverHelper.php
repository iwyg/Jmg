<?php

/**
 * This File is part of the Thapp\JitImage package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\JitImage\Resolver;

use Thapp\JitImage\Parameters;
use Thapp\JitImage\FilterExpression;
use Thapp\JitImage\Cache\CacheInterface;
use Thapp\JitImage\ProcessorInterface;
use Thapp\JitImage\Resource\ImageResource;
use Thapp\JitImage\Loader\LoaderInterface;

/**
 * @trait ImageResolverHelper
 * @package Thapp\JitImage
 * @version $Id$
 */
trait ImageResolverHelper
{
    /**
     * getPath
     *
     * @return string
     */
    private function getPath($path, $source)
    {
        if (null === $path || null !== parse_url($source, PHP_URL_SCHEME)) {
            return $source;
        };

        if (null !== parse_url($path, PHP_URL_PATH)) {
            $slash = DIRECTORY_SEPARATOR;

            return rtrim($path, '\\\/') . $slash . strtr($source, ['/' => $slash]);
        }

        return $path . '/' . $source;
    }

    /**
     * @param ProcessorInterface $processor
     * @param string $source
     * @param array $params
     * @param \Thapp\Image\Cache\CacheInterface $cache
     * @param string $key
     *
     * @access private
     * @return ResourceInterface
     */
    private function applyProcessor(
        ProcessorInterface $processor,
        LoaderInterface $loader,
        $source,
        Parameters $params,
        FilterExpression $filters = null,
        CacheInterface $cache = null,
        $key = null
    ) {
        if (!$loader->supports($source)) {
            throw new \InvalidArgumentException(
                sprintf('Loader "%s" does not support file "%s"', get_class($loader), $source)
            );
        }

        $this->processor->load($loader->load($source));
        $this->processor->process($params, $filters);

        if (null === $cache) {
            return $this->createResource($this->processor);
        }

        $cache->set($key, $this->processor);

        return $cache->get($key);
    }

    /**
     * createResource
     *
     * @param ProcessorInterface $processor
     *
     * @return ResourceInterface
     */
    private function createResource(ProcessorInterface $processor)
    {
        list ($w, $h) = $processor->getTargetSize();

        $resource = new ImageResource(null, $w, $h);

        $resource->setContents($processor->getContents());
        $resource->setFresh(!$processor->isProcessed());
        $resource->setLastModified($processor->getLastModTime());
        $resource->setMimeType($processor->getMimeType());

        // if the image was passed through, we can set a source path
        if (!$processor->isProcessed()) {
            $resource->setPath($processor->getSource());
        }

        return $resource;
    }

    /**
     * extractParams
     *
     * @param string $params
     *
     * @return array
     */
    private function extractParamString($params)
    {
        list ($mode, $width, $height, $gravity, $background) = array_map(function ($value, $key = null) {
            return is_numeric($value) ? (int)$value : $value;
        }, array_pad(explode('/', $params), 5, null));

        if (0 > $mode || 3 < $mode || 0 === $mode) {
            $gravity = null;
        }

        if ($mode !== 3) {
            $background = null;
        }

        if (4 < $mode || 0 === $mode) {
            $height     = null;
            $gravity    = null;
        }

        if (0 == $mode) {
            $width = null;
        }

        $width  = ($mode !== 1 && $mode !== 2) ? $width : (int)$width;
        $height = ($mode !== 1 && $mode !== 2) ? $height : (int)$height;

        return compact('mode', 'width', 'height', 'gravity', 'background');
    }

    /**
     * extractFilters
     *
     * @param mixed $filters
     *
     * @return mixed
     */
    private function extractFilterString($filterStr = null)
    {
        if (null === $filterStr) {
            return;
        }

        if (0 === strpos($filterStr, 'filter:')) {
            $filterStr = substr($filterStr, 7);
        }

        return new FilterExpression;
    }

    /**
     * Return the cache key derived from the url parameters.
     *
     * @param string $path the image source
     * @param string $parameters the parameters as string
     * @param string $filters the filters as string
     *
     * @return string
     */
    private function makeCacheKey(CacheInterface $cache, $name, $src, $paramStr, $filterStr)
    {
        $path = $this->getPath($name, $src);

        return $cache->createKey(
            $path,
            $paramStr.'/'.$filterStr,
            pathinfo($path, PATHINFO_EXTENSION)
        );
    }
}