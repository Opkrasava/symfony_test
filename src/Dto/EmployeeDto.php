<?php
namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class EmployeeDto
{
    #[Assert\NotBlank(message: "Имя обязательно")]
    public string $firstName;

    #[Assert\NotBlank(message: "Фамилия обязательна")]
    public string $lastName;

    #[Assert\NotBlank(message: "Email обязателен")]
    #[Assert\Email(message: "Неверный формат электронной почты")]
    public string $email;

    #[Assert\NotBlank(message: "Дата зачисления обязательна")]
    public string $hireDate;

    #[Assert\NotBlank(message: "Заработная плата обязательна")]
    #[Assert\GreaterThanOrEqual(value: 100, message: "Заработная плата должна быть не менее 100")]
    public float $salary;
}
