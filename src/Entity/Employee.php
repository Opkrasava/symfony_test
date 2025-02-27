<?php

namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Employee",
    title: "Employee",
    description: "Сотрудник компании",
    required: ["firstName", "lastName", "email", "hireDate", "salary"],
    properties: [
        new OA\Property(property: "id", type: "integer", description: "Идентификатор сотрудника"),
        new OA\Property(property: "firstName", type: "string", description: "Имя сотрудника"),
        new OA\Property(property: "lastName", type: "string", description: "Фамилия сотрудника"),
        new OA\Property(property: "email", type: "string", description: "Электронная почта"),
        new OA\Property(property: "hireDate", type: "string", format: "date-time", description: "Дата зачисления"),
        new OA\Property(property: "salary", type: "number", format: "float", description: "Заработная плата")
    ],
)]
#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['email'], message: 'Пользователь с таким email уже существует')]
class Employee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:255)]
    #[Assert\NotBlank(message: "Имя обязательно.")]
    private ?string $firstName = null;

    #[ORM\Column(type:"string", length:255)]
    #[Assert\NotBlank(message: "Фамилия обязательна.")]
    private ?string $lastName = null;

    #[ORM\Column(type:"string", length:255, unique:true)]
    #[Assert\NotBlank(message: "Электронная почта обязательна.")]
    #[Assert\Email(message: "Неверный формат электронной почты.")]
    private ?string $email = null;

    #[ORM\Column(type:"datetime")]
    #[Assert\NotBlank(message: "Дата зачисления обязательна.")]
    #[Assert\GreaterThanOrEqual("today", message: "Дата зачисления не может быть в прошлом")]
    private ?\DateTimeInterface $hireDate = null;

    #[ORM\Column(type:"float")]
    #[Assert\NotBlank(message: "Размер заработной платы обязателен.")]
    #[Assert\GreaterThanOrEqual(100, message: "Заработная плата должна быть не менее 100.")]
    private ?float $salary = null;

    #[ORM\Column(type:"datetime")]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type:"datetime")]
    private ?\DateTimeInterface $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getHireDate(): ?\DateTimeInterface
    {
        return $this->hireDate;
    }
    public function setHireDate(\DateTimeInterface $hireDate): self
    {
        $this->hireDate = $hireDate;
        return $this;
    }

    public function getSalary(): ?float
    {
        return $this->salary;
    }
    public function setSalary(float $salary): self
    {
        $this->salary = $salary;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTime();
    }
}
