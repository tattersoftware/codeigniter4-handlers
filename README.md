# Tatter\Handlers
Cross-module handler registration, for CodeIgniter 4

## Quick Start

1. Install with Composer: `> composer require tatter/handlers`
2. Load the library with the handler path: `$handlers = new \Tatter\Handlers\Handlers('Thumbnails');`
2. Discover classes from any namespace: `$classes = $handlers->findAll();`

## Features

**Handlers** allows developers to define and discover supported classes for dynamic driver
types across all namespaces. An illustration may be more helpful...

### Example

*See also [Tatter\Thumbnails](https://github.com/tattersoftware/codeigniter4-thumbnails)*

You are creating an open source eCommerce site and you want to be able to display thumbnails
for anything in the shop. Some of the products will have simple photos, but others will be
digital content like 3D models, PDF text, or AI-generated data. Your `Thumbnails` library
handles converting a file type into an image, but what if someone else wants to support a
new file type? No problem! They simply create a new handler class in their module under
**src/Thumbnails/** and `Handlers` will detect it. Your example library might look like this:

```
use Tatter\Handlers\Handlers;

class Thumbnails
{
	public function createForFile(string $file)
	{
		$extension = pathinfo($file, PATHINFO_EXTENSION);

		// Initialize Tatter\Handlers
		$handlers = new Handlers('Thumbnails');

		// Get a handler for this file type
		$class = $handlers->where(['extension' => $extension])->first();

		$thumbnail = new $class($file);
		echo $thumbnail->generate();
	}
	...
}
```

## Installation

Install easily via Composer to take advantage of CodeIgniter 4's autoloading capabilities
and always be up-to-date:
* `> composer require tatter/handlers`

Or, install manually by downloading the source files and adding the directory to
`app/Config/Autoload.php`.

## Configuration (optional)

The library's default behavior can be altered by extending its config file. Copy
**examples/Handlers.php** to **app/Config/** and follow the instructions
in the comments. If no config file is found in **app/Config** the library will use its own.

## Usage

**Handlers** uses relative paths to discover files that contain handler classes. Create a
new folder in your project or module with an appropriate name for your implementation,
e.g. **app/Widgets/** or **src/Invoices**.

In order for them be discovered as handlers, your classes need to implement `HandlerInterface`.
For convenience, both a Trait and a base handler are provided as part of this module,
so these are all valid handlers:

```
class MyClass extends \Tatter\Handlers\BaseHandler
{
...

class MyClass implements \Tatter\Handlers\Interfaces\HandlerInterface
{
	use \Tatter\Handlers\Traits\HandlerTrait;
...

class MyClass implements \Tatter\Handlers\Interfaces\HandlerInterface
{
	// Your own implementation
...
```

Your classes will also need an `$attributes` array property to hold identifying information
for each handler. See **src/Interfaces\HandlerInterface.php** for more information.

Once your classes are created, initialize the library with the relative path to your handlers:

	$handlers = new \Tatter\Handlers\Handlers('Widgets');

You can then use the **Handlers** methods to locate all classes, or a subset of classes based
on their attributes:

```
// Iterate through all discovered handlers
foreach ($handlers->findAll() as $class)
{
	$widget = new $class($param1, $param2);
	$widget->display();
}

// ... or get a single handler by one of its attributes
$class = $handlers->where(['color' => 'red'])->first();

// ... or by specifying its name
$class = $handlers->named('FancyHandler');
(new $class)->display();
```

## Caching

**Handlers** scans through all namespaces to discover relevant classes. This distributed
filesystem read can be costly in large projects, so **Handlers** will cache the results
for an amount of time set in the config file (default: one day). Should you need to clear
this cache (for example, when adding a new module with additional handlers) you can use
the "reset" command:

	php spark handlers:reset

Likewise, if you would like to pre-cache handler discovery to improve performance then
you can use the "list" command to discover and cache results:

	php spark handlers:list

Both of these commands rely on preset paths in the config file array `$autoDiscover`, so be
sure to set that before using them.
