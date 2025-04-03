<?php
namespace App\ArgumentResolver;

use App\Attribute\DeserializeApiEntityAttribute;
use App\Dto\EmployeeDto;
use App\Exception\ApiException;
use App\Helper\ApiEntityDtoHelper;
use App\Helper\ControllerAttributeHelper;
use App\Helper\HandleApiErrorsHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Exception;

class UpdateApiEntityResolver implements ValueResolverInterface
{

    public function __construct
    (
        private readonly ValidatorInterface $validator,
        private readonly SerializerInterface $serializer,
        private readonly EntityManagerInterface $entityManager
    )
    {}

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $attribute = ControllerAttributeHelper::getFirstArgumentAttribute(
            $request,
            $argument->getName(),
            DeserializeApiEntityAttribute::class,
            \ReflectionAttribute::IS_INSTANCEOF
        );
        if (!$attribute) {
            return [];
        }
        if ($attribute?->mode != DeserializeApiEntityAttribute::MODE_UPDATE)
        {
            return [];
        }

        $dtoClass = ApiEntityDtoHelper::find($argument->getType());

        $id = $request->attributes->get('id');
        if (!$id) {
            return [];
        }

        $entity = $this->entityManager->getRepository($argument->getType())->find($id);
        if (!$entity) {
            throw new ApiException([], 'Wrong {id} ', 400);
        }

        try {
            /** @var EmployeeDto $dto */
            $dto = $this->serializer->deserialize($request->getContent(), $dtoClass, 'json');
        } catch (Exception $e) {
            throw new ApiException([], 'Wrong format ' . $e->getMessage(), 400);
        }

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            throw new ApiException(HandleApiErrorsHelper::handle($errors), 'Wrong data', 400);
        }

        yield $this->serializer->denormalize($dto, $argument->getType(), 'json', ['object_to_populate' => $entity]);
    }
}
