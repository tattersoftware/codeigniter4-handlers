# Upgrade Guide

## Version 2 to 3
***

Version 3 focuses on simplifying the code and making each class align more logically with what it does.

* `Handlers` has been refactored into `BaseManager`; read more below
* Related, the following have been removed: Handlers service, helper file, and command files
* The `$attributes` property and accessor methods have been replaced by a single static method: `attributes()`
* Identification of handlers is now handled via the static method `handlerId()` instead of the "name" or "uid" attributes, or the class itself

### `BaseManager`

`Handlers` is no longer a library with service and helper. The core of this library is now
centered around an abstract class `BaseManager` with the same discovery and lookup methods
that were previously on `Handlers`. Other libraries needing handler discovery should extend
this class and provide the required `getPath(): string` method.
