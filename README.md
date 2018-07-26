[![Build Status](https://travis-ci.org/systemson/container.svg?branch=master)](https://travis-ci.org/systemson/container)
[![StyleCI](https://styleci.io/repos/126626182/shield?branch=master)](https://styleci.io/repos/126626182)

# Container
Simple PHP DI Container.

## Current State
The project is under testing for conceptual and pattern designing. It may (and most likely will) change a lot from the current state.

Only after the tests are over it will have a final roadmap for a pre-realase, and will be included in packagist.org.

## Instalation

```
composer require amber/container
```

## API (Draft)

### Basic Usage (PSR-11 compliance)

```php
use Amber\Container\Injector;

$container = new Injector();
```

### bind()

```php
$container->bind($key, $value);
```

```php
$container->bind($class);
```

### get()

```php
$container->get($key);
```

### has()

```php
$container->has($key);
```

### unbind()

```php
$container->unbind($key);
```

### bindMultiple()

```php
$container->bindMultiple([$key => $value]);
```

### getMultiple()

```php
$container->getMultiple($keys);
```

### unbindMultiple()

```php
$container->unbindMultiple($keys);
```

### Advanced Usage
Coming soon...
