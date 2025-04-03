<?php
namespace App\Attribute;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class DeserializeApiEntityAttribute
{
    const MODE_CREATE = 'create';
    const MODE_UPDATE = 'update';

    public function __construct(
        public ?string $mode = null
    ) {
    }
}
