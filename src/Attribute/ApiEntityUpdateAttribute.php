<?php
namespace App\Attribute;

#[\Attribute(\Attribute::TARGET_METHOD)]
class ApiEntityUpdateAttribute
{
    public function __construct(
        public ?string $someOption = null
    ) {
    }
}
