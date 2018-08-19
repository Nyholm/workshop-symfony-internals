<?php

declare(strict_types=1);

namespace App;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends BaseKernel
{
    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function (ContainerBuilder $container) use ($loader) {
            $container->loadFromExtension('framework', [
                'router' => [
                    'resource' => 'kernel::loadRoutes',
                    'type' => 'service',
                ],
            ]);

            $confDir = $this->getProjectDir().'/config';
            $loader->load($confDir.'/services.yaml');
            $loader->load($confDir.'/services_'.$this->environment.'.yaml');

            $container->addObjectResource($this);
        });
    }

    public function loadRoutes(LoaderInterface $loader)
    {
        $confDir = $this->getProjectDir().'/config';
        $routes = new RouteCollectionBuilder($loader);
        $routes->import($confDir.'/routes.yaml');

        return $routes->build();
    }

    public function getCacheDir()
    {
        return $this->getProjectDir().'/var/cache/'.$this->environment;
    }

    public function getLogDir()
    {
        return $this->getProjectDir().'/var/log';
    }

    public function getProjectDir()
    {
        return dirname(__DIR__);
    }
}
