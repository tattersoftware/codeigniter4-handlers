# Upgrade Guide

## Version 2 to 3
***

Version 3 focuses on simplifying the code and making each class align more logically with what it does.

* Minimum PHP version has been bumped to `7.4` to match the upcoming framework changes
* All properties that can be typed have been
* `Handlers` has been refactored into `BaseFactory`; read more below
* Related, the following have been removed: Handlers service, helper file, and command files
* The `$attributes` property and accessor methods have been removed
* Identification of handlers is now handled via the class constant `HANDLER_ID` instead of the "name" or "uid" attributes, or the class itself
* The "auto-discovery" feature is removed; read the docs on creating discovery-compatible factories instead

### `BaseFactory`

`Handlers` is no longer a library with service and helper. The core of this library is now
centered around an abstract class `BaseFactory` with simplified lookup methods. Other
libraries needing handler discovery should extend this class and provide the required constants.
