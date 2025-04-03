<?php


namespace App\Helper;

final class ApiEntityDtoHelper
{

    public static function find($class): string
    {
        $entityFqcn = $class;
        $shortClass = (new \ReflectionClass($entityFqcn))->getShortName();
        return 'App\\Dto\\Create'.$shortClass.'DTO';
    }
}
