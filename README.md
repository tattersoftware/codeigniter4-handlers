# Tatter\Handlers
Handler discovery and management, for CodeIgniter 4

[![](https://github.com/tattersoftware/codeigniter4-handlers/workflows/PHPUnit/badge.svg)](https://github.com/tattersoftware/codeigniter4-handlers/actions/workflows/test.yml)
[![](https://github.com/tattersoftware/codeigniter4-handlers/workflows/PHPStan/badge.svg)](https://github.com/tattersoftware/codeigniter4-handlers/actions/workflows/analyze.yml)
[![](https://github.com/tattersoftware/codeigniter4-handlers/workflows/Deptrac/badge.svg)](https://github.com/tattersoftware/codeigniter4-handlers/actions/workflows/inspect.yml)
[![Coverage Status](https://coveralls.io/repos/github/tattersoftware/codeigniter4-handlers/badge.svg?branch=develop)](https://coveralls.io/github/tattersoftware/codeigniter4-handlers?branch=develop)

## Quick Start

1. Install with Composer: `> composer require tatter/handlers`
2. Create a Factory to identify your handlers
2. Discover classes from any namespace: `$widgets = WidgetFactory::findAll();`

## Features

**Handlers** allows developers to define and discover classes of predetermined types
across all namespaces; it is essentially a database-free "model" for classes.

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
e.g. **app/Widgets/** or **src/Reports**.

### Compatibility

In order for them be discovered as handlers your classes need to have a consistent class or
interface type and supply a unique ID via the class constant `HANDLER_ID`.

**Handlers** will resolve class extensions by using this handler ID, so if you want your
app to "replace" a handler from another namespace then simply extend the original class and
leave the `HANDLER_ID` constant the same.

### Factories

Once your handler classes are created you will need a Factory that provides the lookup path
and expected class or interface to identify the handlers. Create the new class extending `BaseFactory`:
```php
<?php

namespace App\Factories;

use App\Interfaces\WidgetInterface;
use Tatter\Handlers\BaseFactory;

class WidgetFactory extends BaseFactory
{
    public const HANDLER_PATH = 'Widgets';
    public const HANDLER_TYPE = WidgetInterface::class;
}
```

You can then use the `BaseFactory` methods to locate all handler classes or a specific
handler by its ID:
```php
use App\Factories\WidgetFactory;

// Iterate through all discovered handlers
foreach (WidgetFactory::findAll() as $class)
{
    $widget = new $class($param1, $param2);
    $widget->display();
}

// ... or get a single handler by specifying its ID
$class = WidgetFactory::find('FancyHandler');
(new $class)->display();
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
**Factories** subfolder and provide them a `HANDLER_ID` constant like any other handler.

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
Browse their code to get an idea of how you might use **Handlers** for your own projects.

* [Tatter\Thumbnails](https://github.com/tattersoftware/codeigniter4-thumbnails): Modular thumbnail generation, for CodeIgniter 4
* [Tatter\Exports](https://github.com/tattersoftware/codeigniter4-exports): Modular file exports, for CodeIgniter 4
