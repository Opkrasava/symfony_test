<?php

namespace App\Helper;

use ReflectionMethod;
use Symfony\Component\HttpFoundation\Request;

final class ControllerAttributeHelper
{
    /**
     * Возвращает массив атрибутов нужного класса, найденных в параметре контроллера с именем $argumentName.
     *
     * @param Request $request
     * @param string $argumentName Имя аргумента (например, "product")
     * @param string $attributeClass Полное имя класса атрибута (например, DeserializeApiEntityAttribute::class)
     * @param int $flags Флаги для ReflectionAttribute (например, ReflectionAttribute::IS_INSTANCEOF)
     *
     * @return array Массив объектов соответствующих атрибутов или пустой массив, если атрибуты не найдены.
     */
    public static function getArgumentAttributes(Request $request, string $argumentName, string $attributeClass, int $flags = 0): array
    {
        $controller = $request->attributes->get('_controller');
        if (!\is_string($controller) || !str_contains($controller, '::')) {
            return [];
        }

        [$class, $method] = explode('::', $controller);

        if (!method_exists($class, $method)) {
            return [];
        }

        $reflectionMethod = new ReflectionMethod($class, $method);
        $parameters = $reflectionMethod->getParameters();
        $targetParameter = null;
        foreach ($parameters as $parameter) {
            if ($parameter->getName() === $argumentName) {
                $targetParameter = $parameter;
                break;
            }
        }

        if ($targetParameter === null) {
            return [];
        }

        $attributes = $targetParameter->getAttributes($attributeClass, $flags);
        if (count($attributes) === 0) {
            return [];
        }

        return array_map(static fn($attr) => $attr->newInstance(), $attributes);
    }

    /**
     * Возвращает первый найденный атрибут для параметра контроллера с именем $argumentName, или null.
     *
     * @param Request $request
     * @param string $argumentName
     * @param string $attributeClass
     * @param int $flags
     *
     * @return mixed
     */
    public static function getFirstArgumentAttribute(Request $request, string $argumentName, string $attributeClass, int $flags = 0): mixed
    {
        $attrs = self::getArgumentAttributes($request, $argumentName, $attributeClass, $flags);
        return $attrs[0] ?? null;
    }
}
