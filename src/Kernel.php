<?php

declare(strict_types=1);

namespace App;

use App\Event\FilterResponseEvent;
use App\Event\GetResponseEvent;
use App\Event\GetResponseForExceptionEvent;
use App\Exception\HttpNotFoundException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Kernel
{
    private $booted = false;
    private $debug;
    private $environment;

    /** @var Container */
    private $container;

    public function __construct(string $env, bool $debug = false)
    {
        $this->debug = $debug;
        $this->environment = $env;
    }

    /**
     * Handle a Request and turn it in to a response.
     */
    public function handle(RequestInterface $request): ResponseInterface
    {
        $this->boot();

        $dispatcher = $this->container->get(EventDispatcherInterface::class);
        try {
            $getResponseEvent = new GetResponseEvent($request);
            $dispatcher->dispatch('kernel.request', $getResponseEvent);

            if (!$getResponseEvent->hasResponse()) {
                throw new HttpNotFoundException($request);
            }

            $filterResponseEvent = new FilterResponseEvent($request, $getResponseEvent->getResponse());
            $dispatcher->dispatch('kernel.response', $filterResponseEvent);
            $response = $filterResponseEvent->getResponse();
        } catch (\Throwable $t) {
            $exceptionEvent = new GetResponseForExceptionEvent($request, $t);
            $dispatcher->dispatch('kernel.exception', $exceptionEvent);
            $response = $exceptionEvent->getResponse();
        }

        return $response;
    }

    public function boot()
    {
        if ($this->booted) {
            return;
        }

        $containerDumpFile = $this->getProjectDir().'/var/cache/'.$this->environment.'/container.php';
        if (!$this->debug && file_exists($containerDumpFile)) {
            require_once $containerDumpFile;
            $container = new \CachedContainer();
        } else {
            $container = new ContainerBuilder();
            $container->setParameter('kernel.project_dir', $this->getProjectDir());
            $container->setParameter('kernel.environment', $this->environment);

            $container->registerForAutoconfiguration(EventSubscriberInterface::class)
                ->addTag('kernel.event_subscriber');
            $container->addCompilerPass(new RegisterListenersPass(EventDispatcherInterface::class), PassConfig::TYPE_BEFORE_REMOVING);

            $loader = new YamlFileLoader($container, new FileLocator($this->getProjectDir().'/config'));
            try {
                $loader->load('services.yaml');
                $loader->load('services_'.$this->environment.'.yaml');
            } catch (FileLocatorFileNotFoundException $e) {
            }

            $container->compile();

            //dump the container
            @mkdir(dirname($containerDumpFile), 0777, true);
            file_put_contents($containerDumpFile, (new PhpDumper($container))->dump(['class' => 'CachedContainer']));
        }

        $this->container = $container;

        $this->booted = true;
    }

    private function getProjectDir()
    {
        return dirname(__DIR__);
    }
}
