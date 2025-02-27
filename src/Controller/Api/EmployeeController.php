<?php
namespace App\Controller\Api;

use App\Dto\EmployeeDto;
use App\Entity\Employee;
use App\Exception\ApiException;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/employees')]
class EmployeeController extends AbstractController
{

    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator
    )
    {}

    private function handleDto(Request $request, $dtoClass) :EmployeeDto
    {
        try {
            /** @var EmployeeDto $dto */
            $dto = $this->serializer->deserialize($request->getContent(), $dtoClass, 'json');
        } catch (\Exception $e) {
            throw new ApiException([], 'Неверный формат данных');
        }

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            throw new ApiException($this->handleErrors($errors), 'Неверный формат данных');
        }
        return $dto;
    }

    private function handleErrors($errors): array {
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[$error->getPropertyPath()] = $error->getMessage();
        }
        return $errorMessages;
    }

    #[Route('', name: 'employee_list', methods: ['GET'])]
    #[OA\Get(
        summary: "Получить список сотрудников",
        responses: [
            new OA\Response(
                response: 200,
                description: "Список сотрудников",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", type: "integer", description: "Идентификатор сотрудника"),
                            new OA\Property(property: "firstName", type: "string", description: "Имя сотрудника"),
                            new OA\Property(property: "lastName", type: "string", description: "Фамилия сотрудника"),
                            new OA\Property(property: "email", type: "string", description: "Электронная почта"),
                            new OA\Property(property: "hireDate", type: "string", format: "date-time", description: "Дата зачисления"),
                            new OA\Property(property: "salary", type: "number", format: "float", description: "Заработная плата")
                        ]
                    )
                )
            )
        ]
    )]
    public function list(EntityManagerInterface $em): Response
    {
        $employees = $em->getRepository(Employee::class)->findAll();
        return $this->json($employees);
    }

    #[Route('', name: 'employee_create', methods: ['POST'])]
    #[OA\Post(
        summary: "Создать сотрудника",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "firstName", type: "string"),
                    new OA\Property(property: "lastName", type: "string"),
                    new OA\Property(property: "email", type: "string"),
                    new OA\Property(property: "hireDate", type: "string", format: "date-time"),
                    new OA\Property(property: "salary", type: "number", format: "float")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Сотрудник создан",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", description: "Идентификатор сотрудника"),
                        new OA\Property(property: "firstName", type: "string", description: "Имя сотрудника"),
                        new OA\Property(property: "lastName", type: "string", description: "Фамилия сотрудника"),
                        new OA\Property(property: "email", type: "string", description: "Электронная почта"),
                        new OA\Property(property: "hireDate", type: "string", format: "date-time", description: "Дата зачисления"),
                        new OA\Property(property: "salary", type: "number", format: "float", description: "Заработная плата")
                    ]
                )
            )
        ]
    )]
    public function create(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        try {
            $dto = $this->handleDto($request, EmployeeDto::class);
        }
        catch (ApiException $exception) {
            return $this->json([$exception->getMessage() => $exception->getErrors()], Response::HTTP_BAD_REQUEST);
        }

        $employee = $this->serializer->denormalize($dto, Employee::class);

        $errors = $validator->validate($employee);
        if (count($errors) > 0) {
            return $this->json(['errors' => $this->handleErrors($errors)], Response::HTTP_BAD_REQUEST);
        }

        $em->persist($employee);
        $em->flush();

        return $this->json($employee, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'employee_update', methods: ['PUT'])]
    #[OA\Put(
        summary: "Обновить данные сотрудника",
        parameters: [
            new OA\Parameter(name: "id", in: "path", description: "ID сотрудника", schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "firstName", type: "string"),
                    new OA\Property(property: "lastName", type: "string"),
                    new OA\Property(property: "email", type: "string"),
                    new OA\Property(property: "hireDate", type: "string", format: "date-time"),
                    new OA\Property(property: "salary", type: "number", format: "float")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Данные сотрудника обновлены",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", description: "Идентификатор сотрудника"),
                        new OA\Property(property: "firstName", type: "string", description: "Имя сотрудника"),
                        new OA\Property(property: "lastName", type: "string", description: "Фамилия сотрудника"),
                        new OA\Property(property: "email", type: "string", description: "Электронная почта"),
                        new OA\Property(property: "hireDate", type: "string", format: "date-time", description: "Дата зачисления"),
                        new OA\Property(property: "salary", type: "number", format: "float", description: "Заработная плата")
                    ]
                )
            )
        ]
    )]
    public function update(int $id, Request $request, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        $employee = $em->getRepository(Employee::class)->find($id);
        if (!$employee) {
            return $this->json(['message' => 'Сотрудник не найден'], Response::HTTP_NOT_FOUND);
        }

        try {
            $dto = $this->handleDto($request, EmployeeDto::class);
        }
        catch (ApiException $exception) {
            return $this->json([$exception->getMessage() => $exception->getErrors()], Response::HTTP_BAD_REQUEST);
        }

        $employee = $this->serializer->denormalize(
            $dto,
            Employee::class,
            'json',
            ['object_to_populate' => $employee]
        );

        $errors = $validator->validate($employee);
        if (count($errors) > 0) {
            return $this->json(['errors' => $this->handleErrors($errors)], Response::HTTP_BAD_REQUEST);
        }

        $em->flush();

        return $this->json($employee);
    }

    #[Route('/{id}', name: 'employee_delete', methods: ['DELETE'])]
    #[OA\Delete(
        summary: "Удалить сотрудника",
        parameters: [
            new OA\Parameter(name: "id", in: "path", description: "ID сотрудника", schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: "Сотрудник удалён"
            )
        ]
    )]
    public function delete(int $id, EntityManagerInterface $em): Response
    {
        $employee = $em->getRepository(Employee::class)->find($id);
        if (!$employee) {
            return $this->json(['message' => 'Сотрудник не найден'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($employee);
        $em->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
