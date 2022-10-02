<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\Logger;
use Doctrine\ORM\EntityManager;
use Exception;
use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use stdClass;

class ProductController extends AutoRotingController
{
    public function __construct(
        private EntityManager $em,
        private Logger $logger
    ) {
    }

    /**
     * Get product by id or list by query
     *
     * @param Request $request
     * @param Response $response
     * @param integer|null $id
     * @return JsonResponse
     */
    public function getAction(Request $request, Response $response, ?int $id = null): JsonResponse
    {
        try {
            $this->logger->log(LogLevel::INFO, "Get request");

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

            return new JsonResponse(
                $product,
                StatusCodeInterface::STATUS_OK
            );
        } catch (HttpNotFoundException $exception) {
            $this->logger->logException($exception);

            return new JsonResponse(
                [
                    "message" => $exception->getMessage()
                ],
                StatusCodeInterface::STATUS_NOT_FOUND
            );
        } catch (Exception $exception) {
            $this->logger->logException($exception);

            return new JsonResponse(
                [
                    "message" => $exception->getMessage()
                ],
                StatusCodeInterface::STATUS_BAD_REQUEST
            );
        }
    }

    /**
     * Add new product
     *
     * @param Request $request
     * @param Response $response
     * @return JsonResponse
     */
    public function postAction(Request $request, Response $response): JsonResponse
    {
        try {
            $this->logger->log(LogLevel::INFO, "Post request");

            $data = $request->getParsedBody();

            $newProduct = $this->createNewProduct($data);

            return new JsonResponse(
                $newProduct,
                StatusCodeInterface::STATUS_CREATED
            );
        } catch (Exception $exception) {
            $this->logger->logException($exception);

            return new JsonResponse(
                [
                    "message" => $exception->getMessage()
                ],
                StatusCodeInterface::STATUS_BAD_REQUEST
            );
        }
    }

    /**
     * Change product
     *
     * @param Request $request
     * @param Response $response
     * @param integer|null $id
     * @return JsonResponse
     */
    public function putAction(Request $request, Response $response, int $id = null): JsonResponse
    {
        try {
            $this->logger->log(LogLevel::INFO, "Put request");

            $repository = $this->em->getRepository(Product::class);

            $product = $repository->findOneBy(compact("id"));

            if (!$product) {
                $newProduct = $this->createNewProduct($request->getParsedBody());

                return new JsonResponse(
                    $newProduct,
                    StatusCodeInterface::STATUS_CREATED
                );
            }

            $product->fillDataFromRequest($request->getParsedBody());

            $this->em->flush();

            $this->em->refresh($product);

            return new JsonResponse(
                $product,
                StatusCodeInterface::STATUS_OK
            );
        } catch (HttpNotFoundException $exception) {
            $this->logger->logException($exception);

            return new JsonResponse(
                [
                    "message" => $exception->getMessage()
                ],
                StatusCodeInterface::STATUS_NOT_FOUND
            );
        } catch (Exception $exception) {
            $this->logger->logException($exception);

            return new JsonResponse(
                [
                    "message" => $exception->getMessage()
                ],
                StatusCodeInterface::STATUS_BAD_REQUEST
            );
        }
    }

    /**
     * Removes a product from database
     *
     * @param Request $request
     * @param Response $response
     * @param integer|null $id
     * @return JsonResponse
     */
    public function deleteAction(Request $request, Response $response, int $id = null): JsonResponse
    {
        try {
            $this->logger->log(LogLevel::INFO, "Delete request");

            $repository = $this->em->getRepository(Product::class);

            $product = $repository->findOneBy(compact("id"));

            if (!$product) {
                throw new HttpNotFoundException($request, "Product {$id} not found.");
            }

            $this->em->remove($product);

            $this->em->flush();

            return new JsonResponse(
                [
                    "message" => "Product removed with success."
                ],
                StatusCodeInterface::STATUS_OK
            );
        } catch (HttpNotFoundException $exception) {
            $this->logger->logException($exception);

            return new JsonResponse(
                [
                    "message" => $exception->getMessage()
                ],
                StatusCodeInterface::STATUS_NOT_FOUND
            );
        } catch (Exception $exception) {
            $this->logger->logException($exception);

            return new JsonResponse(
                [
                    "message" => $exception->getMessage()
                ],
                StatusCodeInterface::STATUS_BAD_REQUEST
            );
        }
    }

    /**
     * Crates a new product
     *
     * @param array|stdClass $data
     * @return Product
     */
    private function createNewProduct(array|stdClass $data): Product
    {
        $newProduct = Product::createFromRequest($data);

        $this->em->persist($newProduct);
        $this->em->flush();

        $this->em->refresh($newProduct);

        return $newProduct;
    }

    public function optionsAction(Request $request, Response $response)
    {
        return $response;
    }
}
