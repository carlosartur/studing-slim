<?php

namespace App\DI;

use App\Controller\UserController;
use App\Controller\ProductController;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use UMA\DIC\Container;
use UMA\DIC\ServiceProvider;
use App\Controller\UserService;
use Slim\Middleware\ContentLengthMiddleware;

final class Slim implements ServiceProvider
{
    public function provide(Container $c): void
    {
        $c->set(UserController::class, static function (ContainerInterface $c) {
            return new UserController(
                $c->get(EntityManager::class)
            );
        });

        $c->set(ProductController::class, static function (ContainerInterface $c) {
            return new ProductController(
                $c->get(EntityManager::class)
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

            UserController::generateRoutes($app);
            ProductController::generateRoutes($app);

            return $app;
        });
    }
}
