<?php

/**************************************************************************
 * Copyright 2018 Glu Mobile Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *************************************************************************/

namespace NspTeam\Reflection;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;

/**
 * ReflectionObject
 *
 * @package NspTeam\Reflection
 */
class ReflectionObject
{
    /**
     * Finds a property for the given class.
     *
     * @param object|string $class The class instance or name.
     * @param string $name The name of a property.
     * @param boolean $access Make the property accessible?
     * @return ReflectionProperty The property.
     * @throws ReflectionException If the property does not exist.
     */
    public static function findProperty($class, string $name, bool $access = true): ReflectionProperty
    {
        $reflection = new ReflectionClass($class);

        while (!$reflection->hasProperty($name)) {
            if (!($reflection = $reflection->getParentClass())) {
                throw new ReflectionException("Class '{$class}' does not have property '{$name}' defined.");
            }
        }

        $property = $reflection->getProperty($name);
        $property->setAccessible($access);

        return $property;
    }

    /**
     * Return current value of a property.
     *
     * @param object|string $class The class instance or name.
     * @param string $name The name of a property.
     * @return mixed The current value of the property.x
     * @throws ReflectionException If the property does not exist.
     */
    public static function getProperty($class, string $name)
    {
        $property = static::findProperty((is_object($class) ? get_class($class) : $class), $name);

        return $property->getValue(is_object($class) ? $class : null);
    }

    /**
     * Set a new value to given property.
     *
     * @param object|string $class The class instance or name.
     * @param string $name The name of a property.
     * @param mixed $value The new value.
     * @return void
     * @throws ReflectionException If the property does not exist.
     */
    public static function setProperty($class, string $name, $value): void
    {
        $property = static::findProperty((is_object($class) ? get_class($class) : $class), $name);
        $property->setValue(is_object($class) ? $class : null, $value);
    }

    /**
     * Get a protected/private static/non-static method from given class.
     *
     * @param object|string $className
     * @param string $methodName
     * @return ReflectionMethod
     * @throws ReflectionException
     */
    public static function getMethod($className, string $methodName): ReflectionMethod
    {
        $r = new ReflectionClass($className);
        $method = $r->getMethod($methodName);
        $method->setAccessible(true);

        return $method;
    }

    /**
     * Call a protected/private static/non-static method from given class.
     *
     * @param string|object $class
     * @param string $methodName
     * @param array $args
     * @return mixed
     * @throws ReflectionException
     */
    public static function callMethod($class, string $methodName, array $args = array())
    {
        $method = self::getMethod((is_object($class) ? get_class($class) : $class), $methodName);
        if ($method->isStatic()) {
            return $method->invokeArgs(null, $args);
        }

        if (!is_object($class)) {
            $r = new ReflectionClass($class);
            // ReflectionMethod
            $rMethod = $r->getConstructor();
            if ($rMethod !==null && $rMethod->getNumberOfRequiredParameters() > 0) {
                throw new ReflectionException("The constructor of class '{$class}' has some required parameters.");
            }
            $class = new $class();
        }

        return $method->invokeArgs($class, $args);
    }
}