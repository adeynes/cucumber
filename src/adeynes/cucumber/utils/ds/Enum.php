<?php
declare(strict_types=1);
/**
 * @link    http://github.com/myclabs/php-enum
 * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */
namespace adeynes\cucumber\utils\ds;

/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 * @author Daniel Costa <danielcosta@gmail.com>
 * @author Mirosław Filip <mirfilip@gmail.com>
 */
abstract class Enum implements \JsonSerializable
{

    protected $value;

    protected static $cache = [];

    public function __construct($value)
    {
        if (!static::isValid($value)) {
            throw new \UnexpectedValueException("Value '$value' is not part of the enum " . \get_called_class());
        }
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getKey()
    {
        return static::search($this->value);
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    final public function equals(self $enum): bool
    {
        return $this->getValue() === $enum->getValue() && get_called_class() === get_class($enum);
    }

    public static function keys(): array
    {
        return array_keys(static::toArray());
    }

    /**
     * @return static[]
     */
    public static function values(): array
    {
        $values = [];
        foreach (static::toArray() as $key => $value) {
            $values[$key] = new static($value);
        }

        return $values;
    }

    public static function toArray(): array
    {
        $class = get_called_class();
        if (!isset(static::$cache[$class])) {
            $reflection = new \ReflectionClass($class);
            static::$cache[$class] = $reflection->getConstants();
        }

        return static::$cache[$class];
    }

    public static function isValid($value): bool
    {
        return in_array($value, static::toArray(), true);
    }

    public static function isValidKey($key): bool
    {
        $array = static::toArray();
        return isset($array[$key]);
    }

    public static function search($value)
    {
        return array_search($value, static::toArray(), true);
    }

    /**
     * Returns a value when called statically like so: MyEnum::SOME_VALUE() given SOME_VALUE is a class constant
     * @param string $name
     * @param mixed $arguments
     * @return static
     * @throws \BadMethodCallException
     */
    public static function __callStatic($name, $arguments)
    {
        $array = static::toArray();
        if (isset($array[$name])) {
            return new static($array[$name]);
        }
        throw new \BadMethodCallException("No static method or enum constant '$name' in class " . get_called_class());
    }

    public function jsonSerialize()
    {
        return $this->getValue();
    }
}