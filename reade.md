[![Latest Stable Version](http://poser.pugx.org/nsp-team/reflection-tool/v)](https://packagist.org/packages/nsp-team/reflection-tool) [![Total Downloads](http://poser.pugx.org/nsp-team/reflection-tool/downloads)](https://packagist.org/packages/nsp-team/reflection-tool) [![Latest Unstable Version](http://poser.pugx.org/nsp-team/reflection-tool/v/unstable)](https://packagist.org/packages/nsp-team/reflection-tool) [![License](http://poser.pugx.org/nsp-team/reflection-tool/license)](https://packagist.org/packages/nsp-team/reflection-tool) [![PHP Version Require](http://poser.pugx.org/nsp-team/reflection-tool/require/php)](https://packagist.org/packages/nsp-team/reflection-tool)

A PHP reflection library to directly access protected/private properties and call protected/private methods.

This library works with major versions of PHP from 5.3 to 7.4.

# Installation

```bash
composer require nsp-team/reflection-tool:~1.0.0
```

# Sample Usage

```php
require __DIR__ . '/vendor/autoload.php';

use NspTeam\Reflection\ReflectionObject;

class Test
{
    private $key;
    private static $keyStatic;

    /**
     * 
     * @return string
     */
    private function one(): string
    {
        return '私有方法';
    }

    /**
     * @param int $i
     * @param int $j
     * @return string
     */
    private static function oneStatic(int $i, int $j): string
    {
        return "私有静态方法 带参 $i 和 $j";
    }
}

$test = new Test();

ReflectionObject::setProperty(Test::class, 'keyStatic', 'another value');
ReflectionObject::setProperty($test, 'key', 'value ');

var_dump(ReflectionObject::callMethod($test, 'one'));
var_dump(ReflectionObject::getProperty($test, 'key'));

var_dump(ReflectionObject::callMethod($test, 'oneStatic', array(1, 2)));
var_dump(ReflectionObject::getProperty($test, 'keyStatic'));

var_dump(ReflectionObject::findProperty($test, 'key'));
var_dump(ReflectionObject::getMethod(Test::class, 'one')->getDocComment());

```