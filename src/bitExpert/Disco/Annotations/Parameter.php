<?php

/*
 * This file is part of the Disco package.
 *
 * (c) bitExpert AG
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace bitExpert\Disco\Annotations;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target({"ANNOTATION"})
 * @Attributes({
 *   @Attribute("name", type = "string"),
 *   @Attribute("default", type = "string"),
 *   @Attribute("required", type = "bool")
 * })
 */
final class Parameter
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var scalar|null
     */
    private $defaultValue;

    /**
     * @var bool
     */
    private bool $required;

    /**
     * Creates a new {@link \bitExpert\Disco\Annotations\Parameter}.
     *
     * @psalm-param array{
     *  value?:array{
     *      name?:string,
     *      default?:scalar,
     *      required?:bool|string
     *  }
     * } $attributes
     * @throws AnnotationException
     */
    public function __construct(array $attributes = [])
    {
        $this->required = true;
        $this->defaultValue = null;

        if (isset($attributes['value'])) {
            if (isset($attributes['value']['name'])) {
                $this->name = $attributes['value']['name'];
            }

            if (isset($attributes['value']['default'])) {
                $this->defaultValue = $attributes['value']['default'];
            }

            if (isset($attributes['value']['required'])) {
                $this->required = AnnotationAttributeParser::parseBooleanValue($attributes['value']['required']);
            }
        }

        if (empty($this->name)) {
            throw new AnnotationException('name attribute missing!');
        }
    }

    /**
     * Returns the name of the configuration value to use.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the default value to use in case the configuration value is not defined.
     *
     * @return scalar|null
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Returns true if the parameter is required, false for an optional parameter.
     *
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }
}
