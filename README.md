# Tatter\Handlers
Cross-module handler management, for CodeIgniter 4

[![](https://github.com/tattersoftware/codeigniter4-handlers/workflows/PHPUnit/badge.svg)](https://github.com/tattersoftware/codeigniter4-handlers/actions/workflows/test.yml)
[![](https://github.com/tattersoftware/codeigniter4-handlers/workflows/PHPStan/badge.svg)](https://github.com/tattersoftware/codeigniter4-handlers/actions/workflows/analyze.yml)
[![](https://github.com/tattersoftware/codeigniter4-handlers/workflows/Deptrac/badge.svg)](https://github.com/tattersoftware/codeigniter4-handlers/actions/workflows/inspect.yml)
[![Coverage Status](https://coveralls.io/repos/github/tattersoftware/codeigniter4-handlers/badge.svg?branch=develop)](https://coveralls.io/github/tattersoftware/codeigniter4-handlers?branch=develop)

## Quick Start

1. Install with Composer: `> composer require tatter/handlers`
2. Load the library with the handler path: `$handlers = new \Tatter\Handlers\Handlers('Thumbnails');`
2. Discover classes from any namespace: `$classes = $handlers->findAll();`

## Features

**Handlers** allows developers to define and discover classes of predetermined types across
all namespaces by a variety of attributes: "a database-free model for classes".

## Installation

Install easily via Composer to take advantage of CodeIgniter 4's autoloading capabilities
and always be up-to-date:
```bash
composer require tatter/handlers
```

Or, install manually by downloading the source files and adding the directory to
**app/Config/Autoload.php**.

## Configuration (optional)

The library's default behavior can be altered by extending its config file. Copy
**examples/Handlers.php** to **app/Config/** and follow the instructions
in the comments. If no config file is found in **app/Config** the library will use its own.

## Usage

**Handlers** uses relative paths to discover files that contain handler classes. Create a
new folder in your project or module with an appropriate name for your implementation,
e.g. **app/Widgets/** or **src/Invoices**.

### Interfaces

In order for them be discovered as handlers, your classes need to implement the interface.
Supply the following static methods then implement `Tatter\Handlers\Interfaces\HandlerInterface`.
* `public static handlerId(): string`: Returns a unique slug identifier for this class
* `public static attributes(): array`: Returns an array of attributes about this handler

If you would like to filter by a more specific version you may supply an additional class
or interface name as the Factory class constant `RETURN_TYPE`.

> Note: See **src/Interfaces/HandlerInterface.php** for more details.

### Factories

Once your handler classes are set up you will need to create a Factory that provides the
lookup path and (optional) interface to identify the handlers. Create the new class and
and extend `BaseFactory`:
```php
<?php

namespace App\Factories;

use Tatter\Handlers\BaseFactory;

class WidgetFactory extends BaseFactory
{
    /**
     * Returns the search path.
     */
    public function getPath(): string
    {
        return 'Widgets';
    }
}
```

You can then use the `BaseFactory` methods to locate all handler classes, or a subset of
classes based on their attributes:
```php
$widgets = new WidgetFactory();

// Iterate through all discovered handlers
foreach ($widgets->findAll() as $class)
{
    $widget = new $class($param1, $param2);
    $widget->display();
}

// ... or get a single handler by one of its attributes
$class = $widgets->where(['color' => 'red'])->first();

// ... or by specifying its handlerId
$class = $widgets->find('FancyHandler');
(new $class)->display();
```

If you want your Factory to search for a more specific interface then add the class string
to the class constant `RETURN_TYPE`:
```php
class WidgetFactory extends BaseFactory
{
    public const RETURN_TYPE = 'App\Interfaces\WidgetInterface';
...
```

## Caching

**Handlers** scans through all namespaces to discover relevant classes. This distributed
filesystem read can be costly in large projects, so **Handlers** will cache the results
for an amount of time set in the config file (default: one day). You can disable Caching
using the config file by setting `$cacheDuration` to `null`.

Often it is a good idea to pre-cache handlers so the filesystem search does not happen on
an actual page load. This library includes `FactoryFactory`, a "Factory to discover other
Factories". If you would like your Factories to be discoverable by `FactoryFactory` and
thus their handlers enabled for auto-caching then place your Factory classes in the
**Factories** subfolder. Factories autodetect their attributes for `HandlerInterface` but
you may supply the overriding methods just like any other handlers.

### Commands

To assist with `FactoryFactory`'s discovery of your factories and their handlers this
library includes two commands that will pre-cache all handler classes and clear the cached
values respectively:
```bash
# Discovers and caches all compatible factories and their handlers
php spark handlers:cache

# Clears all cached factories and handlers
php spark handlers:clear
```

Set your cron job to run `spark handlers:cache` on some interval smaller than the Config
`$cacheDuration` to ensure your handlers are always at hand.

## Examples

Here are some other libraries that implement their own Factory class with a set of handlers.
Browse their code to get an idea of how you might use `Handlers` for your own projects.

* [Tatter\Thumbnails](https://github.com/tattersoftware/codeigniter4-thumbnails): Modular thumbnail generation, for CodeIgniter 4
* [Tatter\Exports](https://github.com/tattersoftware/codeigniter4-exports): Modular file exports, for CodeIgniter 4
