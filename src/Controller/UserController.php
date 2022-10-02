<?php

namespace App\Controller;

use Doctrine\ORM\EntityManager;
use App\Entity\User;
use App\Controller\AutoRotingController;
use Exception;
use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

final class UserController extends AutoRotingController
{
    public function __construct(
        private EntityManager $em,
        private LoggerInterface $logger,
    ) {
    }

    public function getAction(Request $request, Response $response, ?int $id = null)
    {
        try {
            $repository = $this->em->getRepository(User::class);
            $user = $id
                ? $repository->findOneBy(compact("id"))
                : $repository->findByQueryParams($request->getQueryParams());

            if (!$user) {
                throw new HttpNotFoundException($request, "User {$id} not found");
            }

            if (!is_array($user)) {
                $user = [$user];
            }

            return new JsonResponse($user);
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
            $email = $data["email"];

            $newUser = new User($email);

            $this->em->persist($newUser);
            $this->em->flush();

            $this->em->refresh($newUser);

            return new JsonResponse($newUser, StatusCodeInterface::STATUS_CREATED);
        } catch (Exception $exception) {
            return new JsonResponse(["message" => $exception->getMessage()], StatusCodeInterface::STATUS_BAD_REQUEST);
        }
    }
}
