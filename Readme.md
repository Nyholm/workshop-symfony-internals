# Workshop: Symfony internals

All the exercises is on the master branch. See https://github.com/Nyholm/workshop-symfony-internals

Run with 

```
composer update
php -S 127.0.0.1:8080 -t public
```


- What did `RouterListener` do to the `$request` after the `kernel.request` event has
been dispatched? (line 125)
- What is the purpose of `$this->resolver->getController($request)`? (line 132)
- What is the purpose of `$this->argumentResolver->getArguments($request, $controller)`? (line 141)
- What does this line do `$response = \call_user_func_array($controller, $arguments)`? (line 149)