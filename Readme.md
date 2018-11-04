# Workshop: Symfony internals

All the exercises is on the master branch. See https://github.com/Nyholm/workshop-symfony-internals

Run with 

```
composer update
php -S 127.0.0.1:8080 -t public
```

# Exercise 8

The `Symfony\Component\HttpKernel\EventListener\RouterListener` listens to the `kernel.request`
event. Debug the [`HttpKernel::handleRaw`](https://github.com/symfony/symfony/blob/v4.1.3/src/Symfony/Component/HttpKernel/HttpKernel.php#L119-L169)
function to see what is happening there. Prepare short answers  to the following
questions: 

- What did `RouterListener` do to the `$request` after the `kernel.request` event has
been dispatched? (line 125)
- What is the purpose of `$this->resolver->getController($request)`? (line 132)
- What is the purpose of `$this->argumentResolver->getArguments($request, $controller)`? (line 141)
- What does this line do `$response = \call_user_func_array($controller, $arguments)`? (line 149)