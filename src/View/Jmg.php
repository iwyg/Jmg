<?php

/*
 * This File is part of the Thapp\Jmg package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Jmg\View;

use Thapp\Jmg\Parameters;
use Thapp\Jmg\FilterExpression;
use Thapp\Jmg\Cache\CacheInterface;
use Thapp\Jmg\Http\UrlResolverInterace;
use Thapp\Jmg\Resolver\ImageResolverHelper;
use Thapp\Jmg\Resolver\ImageResolverInterface as ImageResolver;
use Thapp\Jmg\Resolver\RecipeResolverInterface as Recipes;
use Thapp\Jmg\Http\UrlBuilder;
use Thapp\Jmg\Http\UrlBuilderInterface as Url;
use Thapp\Jmg\Resource\ResourceInterface;
use Thapp\Jmg\Resource\CachedResourceInterface;

/**
 * @class Jmg
 *
 * @package Thapp\Jmg
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Jmg
{
    use ImageResolverHelper;

    private $pool;
    private $recipes;
    private $generator;
    private $url;
    private $imageResolver;
    private $defaultPath;
    private $cachePrefix;
    private $current;
    private $asTag;
    private $attributes;

    /**
     * Constructor.
     *
     * @param ImageResolver $imageResolver
     * @param RecipeResolverInterface $recipes
     * @param UrlBuilderInterface $url
     */
    public function __construct(ImageResolver $imageResolver, Recipes $recipes, Url $url = null, $cPrefix = 'cached')
    {
        $this->imageResolver = $imageResolver;
        $this->recipes = $recipes;
        $this->url = $url ?: new UrlBuilder;
        $this->pool = [];
        $this->asTag = false;

        $this->start = microtime(true);
        $this->cachePrefix = $cPrefix;
    }

    /**
     * Get the ImageResolver
     *
     * @return ImageResolver
     */
    public function getImageResolver()
    {
        return $this->imageResolver;
    }

    /**
     * Get the RecipesResolver
     *
     * @return RecipesResolverInterface
     */
    public function getRecipesResolver()
    {
        return $this->recipes;
    }

    /**
     * Takes an image source stirng for manipulation.
     *
     * @param string $source the image source path
     * @param string $path the image base path
     *
     * @return Generator
     */
    public function take($source, $path = null, $asTag = false, array $attributes = [])
    {
        $this->current = null;
        $this->setAsTag($asTag, $attributes);

        $path = $path ?: $this->defaultPath;
        $gen = $this->newGenerator();

        $gen->setPath($path);
        $gen->setSource($source);

        return $gen;
    }

    /**
     * Creates an image path from a given recipe
     *
     * @param string $recipe
     *
     * @return string
     */
    public function make($recipe, $source, $asTag = false, array $attributes = [])
    {
        if (!$res = $this->recipes->resolve($recipe)) {
            return '';
        }

        $this->setAsTag($asTag, $attributes);

        list ($prefix, $params, $filter) = $res;

        return $this->apply($prefix, $source, $params, $filter ?: null, $recipe);
    }

    /**
     * apply
     *
     * @return string
     */
    public function apply($name, $source, Parameters $params, FilterExpression $filters = null, $recipe = null)
    {
        if (!$resource = $this->imageResolver->resolve($source, $params, $filters, $name)) {
            return '';
        }

        $this->current = $resource;

        if ($resource instanceof CachedResourceInterface) {
            return $this->getCachedPath($resource, $name);
        }

        if (null !== $recipe) {
            return $this->getRecipeUri($source, $name, $recipe, $params, $filters);
        }

        return $this->getUri($source, $name, $params, $filters);
    }

    /**
     * getUri
     *
     * @param mixed $source
     * @param mixed $name
     * @param Parameters $params
     * @param FilterExpression $filters
     *
     * @return string
     */
    private function getUri($source, $name, Parameters $params, FilterExpression $filters = null)
    {
        return $this->getOutput($this->url->getUri($source, $params, $filters, $name));
    }

    /**
     * getRecipeUri
     *
     * @param mixed $source
     * @param mixed $name
     * @param mixed $recipe
     * @param Parameters $params
     * @param FilterExpression $filters
     *
     * @return string
     */
    private function getRecipeUri($source, $name, $recipe, Parameters $params, FilterExpression $filters = null)
    {
        return $this->getOutput($this->url->getRecipeUri($source, $recipe, $params, $filters));
    }

    /**
     * getCachedPath
     *
     * @param CachedResourceInterface $cached
     * @param mixed $name
     *
     * @return string
     */
    private function getCachedPath(CachedResourceInterface $cached, $name)
    {
        return $this->getOutput($this->url->getCachedUri($cached, $name, $this->cachePrefix));
    }

    /**
     * close
     *
     * @return void
     */
    protected function close()
    {
        $this->imageResolver->getProcessor()->close();
    }

    /**
     * setAsTag
     *
     * @param boolean $asTag
     * @param array $attributes
     *
     * @return void
     */
    protected function setAsTag($asTag, array $attributes)
    {
        if (!(boolean)$asTag) {
            $this->clearTag();

            return;
        }

        $this->asTag = true;
        $this->attributes = $attributes;
    }

    /**
     * getOutput
     *
     * @param string $path
     *
     * @return string
     */
    private function getOutput($path)
    {
        if ($this->asTag) {
            return $this->createTag($path, array_merge($this->attributes, $this->getResourceDimension()));
        }

        return $path;
    }

    /**
     * getResourceDimension
     *
     * @return arra
     */
    private function getResourceDimension()
    {
        return ['width' => $this->current->getWidth(), 'height' => $this->current->getHeight()];
    }

    /**
     * clearTag
     *
     * @return void
     */
    private function clearTag()
    {
        $this->asTag = false;
        $this->attributes = null;
    }

    /**
     * createTag
     *
     * @param string $path
     * @param array $attributes
     *
     * @return string
     */
    private function createTag($path, array $attributes)
    {
        $parts = '';
        foreach ($attributes as $attribute => $value) {
            $parts .= sprintf('%s="%s" ', $attribute, $value);
        }

        return sprintf('<img src="%s" %s/>', $path, $parts);
    }

    /**
     * newGenerator
     *
     * @return Generator
     */
    private function newGenerator()
    {
        if (null === $this->generator) {
            return $this->generator = new Generator($this);
        }
        return clone $this->generator;
    }
}
