# Tatter\Handlers
Cross-module handler registration, for CodeIgniter 4

## Quick Start

1. Install with Composer: `> composer require tatter/handlers`
2. Search for and register handlers: `> php spark handlers:register`

## Features

The Handlers library allows for defining a config file and then registering any
supported handlers across all namespaces.

## Installation

Install easily via Composer to take advantage of CodeIgniter 4's autoloading capabilities
and always be up-to-date:
* `> composer require tatter/handlers`

Or, install manually by downloading the source files and adding the directory to
`app/Config/Autoload.php`.

## Configuration (optional)

The library's default behavior can be altered by extending its config file. Copy
**bin/HandlersConfig.php** to **app/Config/** and follow the instructions
in the comments. If no config file is found in **app/Config** the library will use its own.

## Usage

Run the following command to scan for any supported config files and register the defined handlers:

`> php spark handlers:register`

Handlers may be disabled using the `delete()` method from their corresponding Model.
The `register` command may be rerun anytime to add new or update existing handlers.
