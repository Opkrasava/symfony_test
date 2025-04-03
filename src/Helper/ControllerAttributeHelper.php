<?php


namespace App\Helper;

use ReflectionMethod;
use Symfony\Component\HttpFoundation\Request;

final class ControllerAttributeHelper
{
    /**
     * Возвращает массив атрибутов нужного класса/интерфейса, найденных
     * на методе контроллера. Если атрибутов нет, вернётся пустой массив.
     *
     * @param Request $request
     * @param string $attributeClass Полное имя класса атрибута (например, ApiEntityCreateAttribute::class)
     * @param int $flags Флаги ReflectionAttribute (например ReflectionAttribute::IS_INSTANCEOF)
     *
     * @return array  Массив объектов соответствующих атрибутов
     */
    public static function getMethodAttributes(Request $request, string $attributeClass, int $flags = 0): array
    {
        $controller = $request->attributes->get('_controller');
        // Если _controller отсутствует или не в формате "Class::method"
        if (!\is_string($controller) || !str_contains($controller, '::')) {
            return [];
        }

        [$class, $method] = explode('::', $controller);

        // Если метод не существует
        if (!method_exists($class, $method)) {
            return [];
        }

        $reflectionMethod = new ReflectionMethod($class, $method);

        $attributes = $reflectionMethod->getAttributes($attributeClass, $flags);
        if (count($attributes) === 0) {
            return [];
        }

        // Возвращаем массив объектов (созданных через newInstance())
        return array_map(static fn($attr) => $attr->newInstance(), $attributes);
    }

    /**
     * Возвращает ПЕРВЫЙ найденный атрибут или null, если не найдено.
     */
    public static function getFirstMethodAttribute(Request $request, string $attributeClass, int $flags = 0): mixed
    {
        $attrs = self::getMethodAttributes($request, $attributeClass, $flags);
        return $attrs[0] ?? null;
    }
}
