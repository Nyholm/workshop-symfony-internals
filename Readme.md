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

### Exercise 4: Cache

Branch: [4-event-loop](/../../tree/4-event-loop)

In this exercise we are going to add a cache system. Use your favorite cache library. 
If you do not got a favorite, [php-cache](http://www.php-cache.com/en/latest/) is a 
good one. (`composer require cache/filesystem-adapter`). 

Create a middleware that cache the requests. So the controller is not hit twice. 

### Exercise 5: Container

Branch: [5-cache](/../../tree/5-cache)

This is awesome. Our really fast framework is now even faster. But it is hard to 
update the HTML of your controller in development since the response is cached. 

We want to introduce the concept of "environment" to enable the cache feature
only in "prod". In "dev" environment we want to use a null cache like `cache/void-adapter`.

Install `symfony/dependency-injection` and create `src/Kernelphp` that should be 
responsible for building the container and building the middleware array. A good
idea is to only have one public function: `Kernel::handle(RequestInterface $request): ResponseInterface`. 

**Hint:** To *emit* (send) the response: https://github.com/Nyholm/psr7#emitting-a-response

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
### Exercise 2: Add CLI
### Exercise 3: Event Dispatcher
### Exercise 4: HTTP Foundation
### Exercise 5: Cache
### Exercise 6: HTTP Kernel
### Exercise 7: Router
### Exercise 8: Framework Bundle