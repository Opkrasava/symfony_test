<?php
namespace App\Attribute;

#[\Attribute(\Attribute::TARGET_METHOD)]
class ApiEntityCreateAttribute
{
    public function __construct(
        public ?string $someOption = null
    ) {
    }
}
