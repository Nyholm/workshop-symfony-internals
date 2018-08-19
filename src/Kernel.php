<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\DependencyInjection\AddConsoleCommandPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    public function registerBundles()
    {
        return [];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function (ContainerBuilder $container) use ($loader) {
            $container->registerForAutoconfiguration(EventSubscriberInterface::class)->addTag('kernel.event_subscriber');
            $container->registerForAutoconfiguration(Command::class)->addTag('console.command');

            $container->addCompilerPass(new RegisterListenersPass(EventDispatcherInterface::class), PassConfig::TYPE_BEFORE_REMOVING);
            $container->addCompilerPass(new AddConsoleCommandPass());

            $confDir = $this->getProjectDir().'/config';
            $loader->load($confDir.'/services.yaml');
            $loader->load($confDir.'/services_'.$this->environment.'.yaml');

            $container->addObjectResource($this);
        });
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
