![GitHub last commit](https://img.shields.io/github/last-commit/systemson/container.svg)
[![Latest Stable Version](https://poser.pugx.org/amber/container/v/stable.png)](https://packagist.org/packages/amber/container)
[![Latest Beta Version](https://img.shields.io/packagist/vpre/amber/container.svg)](https://packagist.org/packages/amber/container)
![PHP from Packagist](https://img.shields.io/packagist/php-v/amber/container.svg)
[![Build Status](https://travis-ci.org/systemson/container.svg?branch=master)](https://travis-ci.org/systemson/container)
[![Coverage Status](https://coveralls.io/repos/github/systemson/container/badge.svg?branch=master)](https://coveralls.io/github/systemson/container?branch=master)
[![Total Downloads](https://poser.pugx.org/amber/container/downloads.png)](https://packagist.org/packages/amber/container)
![GitHub](https://img.shields.io/github/license/systemson/container.svg)



# Container
Simple PHP DI Container.

## Instalation

```
composer require amber/container
```

## API (Draft)

### Basic Usage (PSR-11 compliance)

```php
use Amber\Container\Container;

$container = new Container();
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
