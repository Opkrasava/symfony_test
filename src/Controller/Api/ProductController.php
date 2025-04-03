<?php
namespace App\Controller\Api;

use App\Attribute\DeserializeApiEntityAttribute;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/products')]
class ProductController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )
    {}

    #[Route('', name: 'product_create', methods: ['POST'])]
    public function create(
        #[DeserializeApiEntityAttribute(mode: 'create')]
        Product $product
    ): Response
    {
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $this->json([
            'id'    => $product->getId(),
            'title'  => $product->getTitle(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'product_update', methods: ['PUT', 'PATCH'])]
    public function update(
        #[DeserializeApiEntityAttribute(mode: 'update')]
        #[MapEntity(disabled: true)]
        Product $product
    ): Response
    {
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $this->json([
            'id'    => $product->getId(),
            'title'  => $product->getTitle(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->json([
            'id'    => $product->getId(),
            'title'  => $product->getTitle(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
        ], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'product_delete', methods: ['DELETE'])]
    public function delete(Product $product): Response
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
