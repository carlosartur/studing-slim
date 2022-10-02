<?php

namespace App\DI;

use App\Controller\UserController;
use App\Controller\ProductController;
use App\Service\Logger;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use UMA\DIC\Container;
use UMA\DIC\ServiceProvider;
use Slim\Middleware\ContentLengthMiddleware;

final class Slim implements ServiceProvider
{
    public function provide(Container $c): void
    {
        $c->set(Logger::class, static function (ContainerInterface $c) {
            return new Logger();
        });

        $c->set(UserController::class, static function (ContainerInterface $c) {
            return new UserController(
                em: $c->get(EntityManager::class),
                logger: $c->get(Logger::class),
            );
        });

        $c->set(ProductController::class, static function (ContainerInterface $c) {
            return new ProductController(
                em: $c->get(EntityManager::class),
                logger: $c->get(Logger::class),
            );
        });

        $c->set(App::class, static function (ContainerInterface $c): App {
            /** @var array $settings */
            $settings = $c->get('settings');

            $app = AppFactory::create(null, $c);

            $app->addErrorMiddleware(
                $settings['slim']['displayErrorDetails'],
                $settings['slim']['logErrors'],
                $settings['slim']['logErrorDetails']
            );

            $app->add(new ContentLengthMiddleware());
            $app->addBodyParsingMiddleware();

            $app->add(function ($request, $handler) {
                $response = $handler->handle($request);

                return $response
                    ->withHeader('Access-Control-Allow-Origin', '*')
                    ->withHeader('Access-Control-Allow-Headers', '*')
                    ->withHeader('Access-Control-Allow-Methods', '*');
            });

            UserController::generateRoutes($app);
            ProductController::generateRoutes($app);

            return $app;
        });
    }
}
