<?php

namespace App\Controller\Api;

use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/products')]
#[OA\Tag(name: 'Products')]
class ProductController extends AbstractController
{
    private array $products = [
        ['id' => 1, 'name' => 'Smartphone', 'price' => 999.99, 'createdAt' => '2023-01-01T00:00:00+00:00'],
        ['id' => 2, 'name' => 'Laptop', 'price' => 1499.99, 'createdAt' => '2023-01-02T00:00:00+00:00'],
    ];

    #[Route('', name: 'api_products_list', methods: ['GET'])]
    #[OA\Get(
        path: '/api/products',
        summary: 'Get all products',
        tags: ['Products'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of products',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Product')
                )
            )
        ],
        security: [['bearerAuth' => []]]
    )]
    public function list(): JsonResponse
    {
        return $this->json($this->products);
    }

    #[Route('/{id}', name: 'api_products_show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/products/{id}',
        summary: 'Get product by ID',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID of product',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product details',
                content: new OA\JsonContent(ref: '#/components/schemas/Product')
            ),
            new OA\Response(
                response: 404,
                description: 'Product not found'
            )
        ],
        security: [['bearerAuth' => []]]
    )]
    public function show(int $id): JsonResponse
    {
        foreach ($this->products as $product) {
            if ($product['id'] === $id) {
                return $this->json($product);
            }
        }

        return $this->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
    }

    #[Route('', name: 'api_products_create', methods: ['POST'])]
    #[OA\Post(
        path: '/api/products',
        summary: 'Create new product',
        tags: ['Products'],
        requestBody: new OA\RequestBody(
            description: 'Product data',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/Product')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Product created',
                content: new OA\JsonContent(ref: '#/components/schemas/Product')
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid input'
            )
        ],
        security: [['bearerAuth' => []]]
    )]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['name']) || !isset($data['price'])) {
            return $this->json(['error' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
        }

        $newProduct = [
            'id' => max(array_column($this->products, 'id')) + 1,
            'name' => $data['name'],
            'price' => (float)$data['price'],
            'createdAt' => (new \DateTime())->format('c')
        ];

        $this->products[] = $newProduct;

        return $this->json($newProduct, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_products_update', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/products/{id}',
        summary: 'Update product',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID of product to update',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        requestBody: new OA\RequestBody(
            description: 'Updated product data',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/Product')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product updated',
                content: new OA\JsonContent(ref: '#/components/schemas/Product')
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid input'
            ),
            new OA\Response(
                response: 404,
                description: 'Product not found'
            )
        ],
        security: [['bearerAuth' => []]]
    )]
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        foreach ($this->products as &$product) {
            if ($product['id'] === $id) {
                $product['name'] = $data['name'] ?? $product['name'];
                $product['price'] = $data['price'] ?? $product['price'];
                return $this->json($product);
            }
        }

        return $this->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'api_products_delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/products/{id}',
        summary: 'Delete product',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID of product to delete',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Product deleted'
            ),
            new OA\Response(
                response: 404,
                description: 'Product not found'
            )
        ],
        security: [['bearerAuth' => []]]
    )]
    public function delete(int $id): JsonResponse
    {
        foreach ($this->products as $key => $product) {
            if ($product['id'] === $id) {
                unset($this->products[$key]);
                return $this->json(null, Response::HTTP_NO_CONTENT);
            }
        }

        return $this->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
    }
}