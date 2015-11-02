<?php

/**
 * This File is part of the Thapp\Jmg package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Jmg\Validator;

/**
 * @class ModeConstraints implements ValidatorInterface
 * @see ValidatorInterface
 *
 * @package Thapp\Jmg
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class ModeConstraints implements ValidatorInterface
{
    /**
     * constraints
     *
     * @var array
     */
    private $constraints;

    /**
     * Constructor.
     *
     * @param array $constraints
     */
    public function __construct(array $constraints)
    {
        $this->setConstraints($constraints);
    }

    /**
     * Adds a constraint.
     *
     * @param int $mode
     * @param array $values
     *
     * @return void
     */
    public function addConstraint($mode, array $constraints)
    {
        $this->constraints[(int)$mode] = $constraints;
    }

    /**
     * Set constraints.
     *
     * @param array $constraints
     *
     * @return void
     */
    public function setConstraints(array $constraints)
    {
        $this->constraints = [];

        foreach ($constraints as $mode => $constraint) {
            $this->addConstraint($mode, $constraint);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException if mode is unknowen.
     */
    public function validate($mode, array $parameters = [])
    {
        if (!$constraint = $this->getConstraint($mode)) {
            return true;
        }

        // mode 0 has no constraints.
        if (0 === $mode) {
            return true;
        }

        list ($a,  $b)  = array_pad($parameters, 2, null);
        list ($cA, $cB) = array_pad($constraint, 2, null);

        if (5 > $mode) {
            return $this->validateValue($a, $cA) && $this->validateValue($b, $cB);
        }

        //if (4 < $mode && 7 > $mode) {
        //    return $this->validateValue($a, $cA);
        //}

        //if mode 4 < $mode && 7 > $mode:
        return $this->validateValue($a, $cA);
    }

    /**
     * constraint
     *
     * @param int $mode
     *
     * @return array
     */
    private function getConstraint($mode)
    {
        if (isset($this->constraints[$mode])) {
            return $this->constraints[$mode];
        }

        return false;
    }

    /**
     * @param int $value
     * @param int $max
     * @param int $min
     *
     * @access private
     * @return boolean
     */
    private function validateValue($value, $max, $min = 0)
    {
        return $max >= $value && $min <= $value;
    }
}
