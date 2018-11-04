# Workshop: Symfony internals

This workshop slowly builds a framework. 

This workshop requires you to be able to run standard Symfony 4 application. We use PHP 7.1+. 

## Part 1

Below are a short description of each exercise. When you completed one exercise
and starting with the next one. You can continue with your code or "restart" from
the branch mentioned in the exercise description. 

### Exercise 1: HTTP objects

Branch: [1-jbof](/../../tree/1-jbof)

We start of with *jbof*, Just a bunch of files. Nothing will be faster than this.
This is the fastest "framework" you can get. 

But this is not really scalable. Lets start with adding PSR-7 requests and responses.
If you do not have any favorite PSR-7 implementation you could download `nyholm/psr7`.

You can test your application with:

```
php -S 127.0.0.1:8080 
```

### Exercise 2: Controller

Branch: [2-request](/../../tree/2-request)

Let's move "our" code out from index.php. Crete a "controller" class with a function
that takes a request and returns a response. You should put your controllers under
`src/` (maybe you want to create more subfolders).

It is a good practise to have your frontend controller (index.php) in a subdirectory. 
That means the webserver do not have access to the root of you application and can 
directly access any file. Lets put index.php in a `public/` directory.


You can test your application with:

```
php -S 127.0.0.1:8080 -t public
```

### Exercise 3: Event loop

Branch: [3-controller](/../../tree/3-controller)

Excellent. The framework looks pretty good now. But we do not like how we are doing
the routing in index.php. It would mean that we need to edit index.php every time 
we want to add a new controller. 

Let's implement an event loop. Run `composer require "relay/relay:1.1"`. Have a quick
look at [the documentation](http://relayphp.com/) and then create a `RouterMiddleware`
that implements the `MiddlewareInterface` as follows: 

```php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface MiddlewareInterface
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next);
}
```

Make sure to build and run your array of middleware in index.php.

**Note:** As an alternative to `relay/relay` you can use this simple [Runner.php](https://gist.github.com/Nyholm/a7166e6e570738bee75612c3608aa4e3).

### Exercise 4: Cache

Branch: [4-event-loop](/../../tree/4-event-loop)

In this exercise we are going to add a cache system. Use your favorite cache library. 
If you do not got a favorite, [php-cache](http://www.php-cache.com/en/latest/) is a 
good one. (`composer require cache/filesystem-adapter`). 

Create a middleware that cache the requests. So the controller is not hit twice with the same URL. 

### Exercise 5: Container

Branch: [5-cache](/../../tree/5-cache)

This is awesome. Our really fast framework is now even faster. But it is hard to 
update the HTML of your controller in development since the response is cached. 

We want to introduce the concept of "environment" to enable the cache feature
only in "prod". In "dev" environment we want to use a null cache like `cache/void-adapter`.

Install `symfony/dependency-injection` and create `src/Kernel.php` that should be 
responsible for building the container and building the middleware array. A good
idea is to only have one public function: `Kernel::handle(RequestInterface $request): ResponseInterface`. 

The `Kernel` should be responsible for "building" a container which all our services
are loaded. (See [documentation](https://symfony.com/doc/current/components/dependency_injection.html#setting-up-the-container-with-configuration-files))
After the container is built you should run `ContainerBuilder::compile()` before you
use the container.  

**Hint:** To *emit* (send) the response: https://github.com/Nyholm/psr7#emitting-a-response

#### Bonus exercise

For performance reasons, we should not build the container at every request in production environment. 
We should used a cached/dumped container. See the [Symfony documentation](https://symfony.com/doc/current/components/dependency_injection/compilation.html#dumping-the-configuration-for-performance)
about how to dump a container.   

### Exercise 6: Security 

Branch: [6-container](/../../tree/6-container)

Looking better and better now. We want to have that "admin" stuff we had in **jbof**. 
Lets add some security. Lets first separate *Authentication* (Who are you?) from 
*Authorization* (What are you allowed to do?). These are two new *things* so we 
need new middleware.

For this exercise, make sure to create an admin controller that only the user
"alice" can see. 
You do not need to validate passwords. 

**Note:** If you return with a response like: 
```php
return new Response(401, ['WWW-Authenticate'=>'Basic realm="Admin area"'], 'This page is protected');
```
A HTTP Basic authentication login window will show for the user. Read the input to that window by: 

```php
$auth = $request->getServerParams()['PHP_AUTH_USER'] ?? '';
$pass = $request->getServerParams()['PHP_AUTH_PW'] ?? '';
``` 

### Exercise 7: Toolbar 

Branch: [7-security](/../../tree/7-security)

When in "dev" environment, it is nice to have a toolbar that shows some statistics
about the request. Lets try to implement that. Since this is a new feature we need
a new middleware. 

The toolbar should be added just before `</body>` of the response. 

**Note:** To gather statistics from ie the cache service you need to create a decorator
that [decorates the service](https://symfony.com/doc/current/service_container/service_decoration.html).

```php
class CacheDataCollector implements CacheItemPoolInterface
{
    private $real;
    private $calls;

    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->real = $cache;
    }

    public function getCalls()
    {
        return $this->calls;
    }

    public function getItem($key)
    {
        $this->calls['getItem'][] = ['key'=>$key];
        return $this->real->getItem($key);
    }
    // ...
```


### Exercise 8: Exception 

Branch: [8-toolbar](/../../tree/8-toolbar)

Lets create this controller: 

```php
use Psr\Http\Message\RequestInterface;

class ExceptionController
{
    public function run(RequestInterface $request)
    {
        throw new \RuntimeException('This is an exception');
    }
}
```

We want to print a helpful message in "dev" environment and a pretty "I'm sorry"
page in "prod". Since this is a new feature we need a new middleware.

## Part 2

We built a real good framework now. It is a simple Symfony. Since we like the
Symfony ecosystem so much. Lets try to refactor our framework to use more Symfony
components. 


### Exercise 1: Use Autowiring

Branch: [9-exception](/../../tree/9-exception)

(Only do this if you got time and energy. This exercise could easily be skipped.)

We love autowiring. It makes our service configuration small and nice. Try to
autowire as many services as you can. The [Symfony documentation](https://symfony.com/doc/current/service_container.html)
may be a good reference.

### Exercise 2: Add CLI

Branch: [21-autowire](/../../tree/21-autowire)

The Command Line Interface is just another frontend controller. The code is similar
to our index.php. You should require `symfony/console` and create a `./bin/console`
file.

You should also create a small command class to test your `./bin/console`.

#### Bonus exercise

Make sure you can register your command in the service container. This allows 
command classes to use dependency injection as normal. 

**Hint:** There is a class `Symfony\Component\Console\CommandLoader\ContainerCommandLoader`
that is registered With the `AddConsoleCommandPass`.

```php
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\DependencyInjection\AddConsoleCommandPass;

$container->registerForAutoconfiguration(Command::class)->addTag('console.command');
$container->addCompilerPass(new AddConsoleCommandPass());
```

### Exercise 3: Event Dispatcher

Branch: [22-command](/../../tree/22-command)

The time has come for us to replace the heart of our application, the event loop.
This is a major refactoring and we need to update all our middleware. Start by
downloadning `symfony/event-dispatcher`. Instead of having one "loop" as we did
with `relay/relay` we can now create multiple loops (or events). 

Create event classes for: 
* An incoming request.
* Parsing/Filtering of a response
* Exception

Our middlewares should be refactored to `EventSubscribers`. The `Kernel::handle`
is also subject for a rewrite. 

The benefit of different "loops" (or events) is that we can show the toolbar on 
an exception page. That was not possile before. 

**Hint:** One feature of `symfony/event-dispatcher` is that we can use 
`$event->stopPropagation()` which stops the current loop. That could be 
useful in our cache or security middleware. 

### Exercise 4: HTTP Foundation

Branch: [23-event-dispatcher](/../../tree/23-event-dispatcher)

To fully be able to integrate with symfony components we should be using Symfonys
implementation of request and responses. So lets remove PSR-7 and use `symfony/http-foundation`.

It is a lot to rewrite but it is just simple changes. Feel free to skip ahead
to next exercise. 

### Exercise 5: Cache

Branch: [24-http-foundation](/../../tree/24-http-foundation)

PHP-cache is great. But we are moving towards full Symfony. Lets use 
`symfony/cache` instead. 

**Note:** This is a simple change becuase PSR-6 is awesome. 

### Exercise 6: HTTP Kernel

Branch: [25-cache](/../../tree/25-cache)

We've done a lot of heavy lifting ourself in our `App\Kernel`. Let `symfony/http-kernel`
be responsible for that from now on. Our `App\Kernel` should extend `Symfony\Component\HttpKernel\Kernel`
but we still need to define where our configuration is located. 

The Symfony kernel uses a `HttpKernel` to handle the request. This is done automatically
if you register a `http_kernel` service:

```yaml
  http_kernel:
    class: Symfony\Component\HttpKernel\HttpKernel
    public: true
    arguments:
      - '@Symfony\Component\EventDispatcher\EventDispatcherInterface'
      - '@Symfony\Component\HttpKernel\Controller\ControllerResolver'

  Symfony\Component\HttpKernel\Controller\ControllerResolver: ~
```

### Exercise 7: Router

Branch: [26-http-kernel](/../../tree/26-http-kernel)

Symfony 4.1 has the quickest router implemented in PHP. Lets start using it. 
We want to remove our `Router` middleware and define our routes in `./config/routes.yaml`
instead. We are not using the FrameworkBundle just yet so we need to look at 
the documentation for the [routing **component**](http://symfony.com/doc/current/components/routing.html#the-all-in-one-router).

**Note:** Make sure to register `Symfony\Component\HttpKernel\EventListener\RouterListener`
in the service container. 

### Exercise 8: Http Kernel (important)

Branch: [27-router](/../../tree/27-router)

The `Symfony\Component\HttpKernel\EventListener\RouterListener` listens to the `kernel.request`
event. Debug the [`HttpKernel::handleRaw`](https://github.com/symfony/symfony/blob/v4.1.3/src/Symfony/Component/HttpKernel/HttpKernel.php#L119-L169)
function to see what is happening there. Prepare short answers  to the following
questions: 

- What did `RouterListener` do to the `$request` after the `kernel.request` event has
been dispatched? (line 125)
- What is the purpose of `$this->resolver->getController($request)`? (line 132)
- What is the purpose of `$this->argumentResolver->getArguments($request, $controller)`? (line 141)
- What does this line do `$response = \call_user_func_array($controller, $arguments)`? (line 149)

### Exercise 9: Framework Bundle

Branch: [27-router](/../../tree/27-router)

We are almost there. Let's start using the FrameworkBundle. This bundle helps you register
a lot of services. Make sure to enable the FrameworkBundle in `App\Kernel`. What can 
you remove now? Maybe the router configuration? 

You could also have a look at `Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait`. It
could be a good fit for our `App\Kernel`.

### Exercise 10: Symfony Flex

Branch: [28-framework-bundle](/../../tree/28-framework-bundle)

This is the end of the workshop. You could create a new Symfony Flex probject with
`composer create-project symfony/skeleton my_projecet`. Compare the differencis between a fresh
install of symfony with the framework you built. 

