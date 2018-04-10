[![Build Status](https://travis-ci.org/systemson/Container.svg?branch=master)](https://travis-ci.org/systemson/Container)
[![StyleCI](https://styleci.io/repos/126626182/shield?branch=master)](https://styleci.io/repos/126626182)

# Container
Simple PHP DI Container.

## Current State
The project is under testing for conceptual and pattern designing. It may (and most likely will) change a lot from the current state.

Only after the tests are over it will have a final roadmap for a pre-realase, and will be included in packagist.org.

## API
**Usage**

```php
use Amber\Container\Injector;

$container = new Injector();
```

### bind()
Adds a dependency to the container map. If it's a class it won't be instantiated untill it's required.
```php
$container->bind($key, $value);
```

### to() (todo)
```php
$container->bind($key, $value)->to($class);
```

### has()
Checks if a dependency exists in the container map by its unique $key.
```php
$container->has($key);
```

### get()
Gets and instantiate (if required) a depdendecy from the container map.
```php
$container->get($key);
```

### unbind()
Removes a depdendency from the container map.
```php
$container->unbidn($key);
```

### getInstanceOf()
Insantiates the class, passing the required params to the constructor if they exists in the container map, or if the container can find the required class(es). Then stores a serialized instance of the class in the caché, so the container don't need to reinstantiate everytime it's required.
```php
$container->getInstanceOf($class);
```

### with() (todo)
```php
$container->getInstanceOf($class)->with($args);
```

### setInstance() (todo)
Stores or Override a serialized instante of the object in the cache and Adds it to the container map,  so the container can use this instead of a brand new one.
```php
$container->setInstance($object);
```
