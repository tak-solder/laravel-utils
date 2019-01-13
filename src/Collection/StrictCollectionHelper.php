<?php

namespace TakSolder\LaravelUtils\Collection;

use Illuminate\Support\Collection;

class StrictCollectionHelper
{
    private static $scholars = [
        'bool' => 'is_bool',
        'boolean' => 'is_bool',
        'int' => 'is_int',
        'integer' => 'is_int',
        'float' => 'is_float',
        'double' => 'is_float',
        'string' => 'is_string',
        'object' => 'is_object',
        'resource' => 'is_resource',
        'null' => 'is_null',
    ];

    /**
     * @param Collection $collection
     * @param string $type
     * @return bool
     * @throws \Exception
     */
    public static function of(Collection $collection, string $type)
    {
        $func = self::getDetectFunction($type);
        foreach ($collection as $item) {
            if (!call_user_func($func, $item)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param Collection $collection
     * @param string $type
     * @throws InvalidTypeException|\Exception
     */
    public static function excepts(Collection $collection, string $type)
    {
        $func = self::getDetectFunction($type);
        foreach ($collection as $item) {
            if (call_user_func($func, $item)) {
                continue;
            }

            if (isset(self::$scholars[strtolower($type)])) {
                $given = gettype($item);
            } else {
                $given = get_class($item);
            }

            throw new InvalidTypeException("collection items except {$type}, {$given} given.");
        }
    }

    /**
     * @param Collection $collection
     * @param string $type
     * @return Collection
     * @throws \Exception
     */
    public static function filterOf(Collection $collection, string $type)
    {
        $func = self::getDetectFunction($type);
        return $collection->filter($func);
    }

    /**
     * @param Collection $collection
     * @param string $type
     * @return Collection
     * @throws \Exception
     */
    public static function onlyOf(Collection $collection, string $type): Collection
    {
        $func = self::getDetectFunction($type);
        return $collection->filter($func);
    }

    /**
     * @param string $type
     * @return \Closure|callable
     * @throws \Exception
     */
    private static function getDetectFunction(string $type)
    {
        switch (true) {
            case isset(self::$scholars[strtolower($type)]):
                $func = self::$scholars[strtolower($type)];
                break;

            case class_exists($type) || interface_exists($type):
                $func = function ($item) use ($type) {
                    return $item instanceof $type;
                };
                break;

            default:
                throw new \Exception('unknown type : ' . $type);
        }

        return $func;
    }
}
