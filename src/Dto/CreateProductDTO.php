<?php
namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateProductDTO
{
    #[Assert\NotBlank(message: "Title id required")]
    public string $title;

    #[Assert\NotBlank(message: "Description is required")]
    public string $description;

    #[Assert\NotBlank(message: "Price is required")]
    #[Assert\GreaterThanOrEqual(value: 10, message: "Product price must be more 10")]
    public string $price;
}
