<?php

namespace App\DI;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use UMA\DIC\Container;
use UMA\DIC\ServiceProvider;

/**
 * A ServiceProvider for registering Controller related to
 * Doctrine in a DI container.
 *
 * If the project had custom repositories (e.g. UserRepository)
 * they could be registered here.
 */
final class Doctrine implements ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function provide(Container $c): void
    {
        $c->set(EntityManager::class, static function (ContainerInterface $c): EntityManager {
            /** @var array $settings */
            $settings = $c->get('settings');

            $config = ORMSetup::createAttributeMetadataConfiguration(
                $settings['doctrine']['metadata_dirs'],
                $settings['doctrine']['dev_mode'],
                null,
                $settings['doctrine']['dev_mode'] ?
                    new FilesystemAdapter(directory: $settings['doctrine']['cache_dir']) :
                    new ArrayAdapter()
            );

            return EntityManager::create($settings['doctrine']['connection'], $config);
        });
    }
}
