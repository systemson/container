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
#### Binds an item to the Container's map by a unique key.
bind(string $key, mixed $value) : boolean
**param** string $key *The unique item's key.*
**param** mixed  $value *The value of the item.*
**return** bool *True on success. False if key already exists.*

Bind an Service to the container by a unique key.
```php
$container->bind($key, $value);
```

Or bind a class like this.
```php
$container->bind($class);
```

### get()
#### Gets an item from the Container's map by its unique key
get(string $key): mixed
**param** string $key *The unique item's key.
**return** mixed *The value of the item.*
```php
$container->get($key);
```

### has()
#### Checks for the existance of an item on the Container's map by its unique key.
has(string $key): bool
**param** string $key *The unique item's key.
**return** bool
```php
$container->has($key);
```

### unbind()
has(string $key): bool
#### Unbinds an item from the Container's map by its unique key.
unbind(string $key): bool
**param** string $key *The unique item's key.
**return** bool *true on success, false on failure.*
```php
$container->unbind($key);
```

## Multiple actions
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

### make()
#### Binds and Gets a Service from the Container's map by its unique key.
make(string $class): mixed
**param** string $class *The item's class.*
**return** mixed *The value of the item.*
```php
$container->make($class);
```

### register()
#### Binds an item to the Container and return the ServiceClass.
register(string $class, string $alias = null): ServiceClass
**param** string $class *The item's class.*
**param** string $alias *The item's alias.*
**return** ServiceClass

```php
$container->register($class);
```
### singleton()
#### Binds an item to the Container as singleton and return theServiceClassservice.
singleton(string $class, string $alias = null): ServiceClass
**param** string $class *The item's class.*
**param** string $alias *The item's alias.*
**return** ServiceClass

```php
$container->singleton($class);
```



More coming soon...
