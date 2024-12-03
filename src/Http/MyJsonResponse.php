<?php

declare(strict_types=1);

namespace App\Http;

use Symfony\Component\HttpFoundation\JsonResponse;
use ReflectionClass;
use ReflectionProperty;

/**
 * Summary of MyJsonResponse
 *
 * A custom JsonResponse class that converts objects to associative arrays before encoding them to JSON.
 */
class MyJsonResponse extends JsonResponse
{
    public function __construct($objects, int $status = 200, array $headers = [], bool $json = false)
    {
        $data = is_array($objects) ? array_map([$this, 'objectToArray'], $objects) : $this->objectToArray($objects);
        parent::__construct($data, $status, $headers, $json);
    }

    /**
     * Convert an object to an associative array
     *
     * @param object $object
     * @return array
     */
    private function objectToArray(object $object): array
    {
        if (method_exists($object, 'toArray')) {
            return $object->toArray();
        }

        $reflectionClass = new ReflectionClass($object);
        $properties = $reflectionClass->getProperties(
            ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE
        );

        $data = [];
        foreach ($properties as $property) {
            $property->setAccessible(true);
            $data[$property->getName()] = $property->getValue($object);
        }

        return $data;
    }
}
