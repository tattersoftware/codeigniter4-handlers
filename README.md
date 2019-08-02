# Tatter\Handlers
Cross-module class adapter registration, for CodeIgniter 4

## Quick Start

1. Install with Composer: `> composer require tatter/handlers`
2. Update the database: `> php spark migrate:latest -all`
3. Search for and register handlers: `> php spark handlers:register`

## Features

The Handlers library allows for defining a type of handler and then registering any
supported adapters across all namespaces.

## Installation

Install easily via Composer to take advantage of CodeIgniter 4's autoloading capabilities
and always be up-to-date:
* `> composer require tatter/handlers`

Or, install manually by downloading the source files and adding the directory to
`app/Config/Autoload.php`.

Once the files are downloaded and included in the autoload, run any library migrations
to ensure the database is setup correctly:
* `> php spark migrate:latest -all`

**Pro Tip:** You can add the spark command to your composer.json to ensure your database is
always current with the latest release:
```
{
	...
    "scripts": {
        "post-update-cmd": [
            "composer dump-autoload",
            "php spark migrate:latest -all"
        ]
    },
	...
```

## Configuration (optional)

The library's default behavior can be altered by extending its config file. Copy
**bin/Handlers.php** to **app/Config/** and follow the instructions
in the comments. If no config file is found in **app/Config** the library will use its own.

## Usage

Once the database is migrated, run the following command to scan for any supported
handlers and register their adapters:

`> php spark handlers:register`

Adapters may be disabled using the `delete()` method from their corresponding Model.
The `register` command may be rerun anytime to add new or update existing adapters.
