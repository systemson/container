[![Latest Stable Version](https://poser.pugx.org/amber/container/v/stable.png)](https://packagist.org/packages/amber/container)
[![Latest Beta Version](https://img.shields.io/packagist/vpre/amber/container.svg)](https://packagist.org/packages/amber/container)
[![Build Status](https://travis-ci.org/systemson/container.svg?branch=master)](https://travis-ci.org/systemson/container)
[![PHP-Eye](https://php-eye.com/badge/amber/container/tested.svg?style=flat)](https://php-eye.com/package/amber/container)
[![Coverage Status](https://coveralls.io/repos/github/systemson/container/badge.svg?branch=master)](https://coveralls.io/github/systemson/container?branch=master)
[![StyleCI](https://styleci.io/repos/126626182/shield?branch=master)](https://styleci.io/repos/126626182)
[![Total Downloads](https://poser.pugx.org/amber/container/downloads.png)](https://packagist.org/packages/amber/container)


# Container
Simple PHP DI Container.

## Current State
The project is under testing for conceptual and pattern designing. It may (and most likely will) change a lot from the current state.

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
