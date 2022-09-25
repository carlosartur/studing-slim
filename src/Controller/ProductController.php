<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManager;
use Exception;
use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class ProductController extends AutoRotingController
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getAction(Request $request, Response $response, ?int $id = null)
    {
        try {
            $repository = $this->em->getRepository(Product::class);

            $product = $id
                ? $repository->findOneBy(compact("id"))
                : $repository->findByQueryParams($request->getQueryParams());

            if (!$product) {
                throw new HttpNotFoundException($request, "Product {$id} not found");
            }

            if (!is_array($product)) {
                $product = [$product];
            }

            return new JsonResponse($product);
        } catch (HttpNotFoundException $exception) {
            return new JsonResponse(["message" => $exception->getMessage()], StatusCodeInterface::STATUS_NOT_FOUND);
        } catch (Exception $exception) {
            return new JsonResponse(["message" => $exception->getMessage()], StatusCodeInterface::STATUS_BAD_REQUEST);
        }
    }

    public function postAction(Request $request, Response $response)
    {
        try {
            $data = $request->getParsedBody();
            $newProduct = Product::createFromRequest($data);

            $this->em->persist($newProduct);
            $this->em->flush();

            $this->em->refresh($newProduct);

            return new JsonResponse($newProduct, StatusCodeInterface::STATUS_CREATED);
        } catch (Exception $exception) {
            return new JsonResponse(["message" => $exception->getMessage()], StatusCodeInterface::STATUS_BAD_REQUEST);
        }
    }

    public function putAction(Request $request, Response $response)
    {
    }
}
