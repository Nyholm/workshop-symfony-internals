<?php

declare(strict_types=1);

namespace App;

use Nyholm\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

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

        $middlewares[] = $this->container->get('middleware.auth');
        $middlewares[] = $this->container->get('middleware.security');
        $middlewares[] = $this->container->get('middleware.cache');
        $middlewares[] = new \App\Middleware\Router($this->container);

        $runner = (new \Relay\RelayBuilder())->newInstance($middlewares);

        return $runner($request, new Response());
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

            $loader = new YamlFileLoader($container, new FileLocator($this->getProjectDir().'/config'));
            try {
                $loader->load('services.yaml');
                $loader->load('services_'.$this->environment.'.yaml');
            } catch (FileLocatorFileNotFoundException $e) {
            }

            $container->compile();

            //dump the container
            @mkdir(dirname($containerDumpFile), 0777, true);
            file_put_contents($containerDumpFile, (new PhpDumper($container))->dump(array('class' => 'CachedContainer')));
        }

        $this->container = $container;

        $this->booted = true;
    }

    private function getProjectDir()
    {
        return dirname(__DIR__);
    }
}
