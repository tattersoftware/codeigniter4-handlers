<?php

namespace Tatter\Handlers;

use CodeIgniter\Config\Factories;
use RuntimeException;
use Throwable;

/**
 * Abstract Base Factory Class
 *
 * An abstract class with common static functions for path-specific
 * handler discovery.
 * Required:
 * - public class constant HANDLER_PATH, the namespace search path
 * - public class constant HANDLER_TYPE, the class string of the types to discover
 * Optional, to be included in automated caching:
 * - public class constant HANDLER_ID, to be included
 * - place extending classes in the Factories subfolder
 *
 * Additionally each handler class must have a string identifier
 * unique to the discovery path as its public constant HANDLER_ID.
 */
abstract class BaseFactory
{
    public const HANDLER_PATH = '';
    public const HANDLER_TYPE = '';

    /**
     * Array of paths and their discovered handlers.
     *
     * @var array<string, array<string, class-string>> [Path => [ID => Class]]
     */
    private static array $discovered = [];

    /**
     * Resets discovered handlers. Mostly just for testing.
     */
    public static function reset(): void
    {
        if (static::HANDLER_PATH !== '') {
            unset(self::$discovered[static::HANDLER_PATH]);
        } else {
            self::$discovered = [];
        }
    }

    /**
     * Returns a handler by its ID.
     *
     * @throws RuntimeException If a handler mathcing the ID was not found.
     *
     * @return class-string
     */
    final public static function find(string $id): string
    {
        $handlers = self::findAll();

        if (! isset($handlers[$id])) {
            throw new RuntimeException('Unknown handler "' . $id . '" for ' . static::class);
        }

        return $handlers[$id];
    }

    /**
     * Returns an array of all discovered handlers
     * classes indexed by ID.
     *
     * @return array<string, class-string>
     */
    final public static function findAll(): array
    {
        if (! isset(self::$discovered[static::HANDLER_PATH])) {
            self::discover();
        }

        return self::$discovered[static::HANDLER_PATH];
    }

    /**
     * Iterates through namespaces and finds classes
     * matching HANDLER_TYPE in the search path.
     *
     * @throws RuntimeException For unrelated handlers having ID collision
     */
    private static function discover(): void
    {
        // Check the cache first
        if (self::restoreCache()) {
            return;
        }

        service('timer')->start('Discover ' . static::HANDLER_PATH);

        // Have to do this the hard way
        $locator = service('locator');
        $ignored = config('Handlers')->ignoredClasses;

        // Scan each namespace
        self::$discovered[static::HANDLER_PATH] = [];

        foreach (service('autoloader')->getNamespace() as $namespace => $paths) {
            // Check for files in the path for this namespace
            foreach ($locator->listNamespaceFiles($namespace, static::HANDLER_PATH) as $file) {
                // Try to get the class name
                if (! $class = self::getHandlerClass($file, $namespace)) {
                    continue;
                }

                // Skip ignored classes
                if (in_array($class, $ignored, true)) {
                    continue;
                }

                // A match! Get the ID
                $id = $class::HANDLER_ID;

                // If no overlap, or if the new class is a child of the current then store and move on
                if (! isset(self::$discovered[static::HANDLER_PATH][$id]) || is_a($class, self::$discovered[static::HANDLER_PATH][$id], true)) {
                    self::$discovered[static::HANDLER_PATH][$id] = $class;

                    continue;
                }

                // If the classes are not related then it is a conflict
                if (! is_a(self::$discovered[static::HANDLER_PATH][$id], $class, true)) {
                    throw new RuntimeException('Handlers have conflicting ID "' . $id . '": ' . self::$discovered[static::HANDLER_PATH][$id] . ', ' . $class);
                }
            }
        }

        ksort(self::$discovered[static::HANDLER_PATH], SORT_STRING);

        // Cache the results
        self::commitCache();

        service('timer')->stop('Discover ' . static::HANDLER_PATH);
    }

    /**
     * Validates that a file path contains a HandlerInterface
     * (and optional RETURN_TYPE) and returns its full class name.
     *
     * @param string $file      Full path to the file in question
     * @param string $namespace The file's namespace
     *
     * @return string|null The fully-namespaced class
     *
     * @internal
     */
    final public static function getHandlerClass(string $file, string $namespace): ?string
    {
        // Skip non-PHP files
        if (substr($file, -4) !== '.php') {
            return null;
        }

        // Make sure the file has been loaded
        try {
            include_once $file;
        }
        // @codeCoverageIgnoreStart
        catch (Throwable $e) {
            return null;
        }
        // @codeCoverageIgnoreEnd

        // Build the fully-namespaced class
        $class = $namespace . '\\' . static::HANDLER_PATH . '\\' . basename($file, '.php');

        // Verify that the class is available
        if (! class_exists($class, false)) {
            return null;
        }

        // Must have a HANDLER_ID to count
        if (! defined("{$class}::HANDLER_ID")) {
            return null;
        }

        // Verify the HANDLER_TYPE
        if (! is_a($class, static::HANDLER_TYPE, true)) {
            return null;
        }

        return $class;
    }

    //--------------------------------------------------------------------

    /**
     * Returns the cache key for this Factory.
     */
    public static function cacheKey(): string
    {
        return 'handlers-' . mb_url_title(static::HANDLER_PATH, '-', true);
    }

    /**
     * Removes discovered classes from the cache.
     */
    final public static function clearCache(): void
    {
        cache()->delete(static::cacheKey());
    }

    /**
     * Commits discovered classes to the cache. Called after discovery.
     */
    final protected static function commitCache(): void
    {
        $config = config('Handlers');

        if ($config->cacheDuration !== null) {
            cache()->save(static::cacheKey(), self::$discovered[static::HANDLER_PATH], $config->cacheDuration);
        }
    }

    /**
     * Loads any discovered classes from the cache.
     *
     * @return bool Whether there was cache content to restore
     */
    final protected static function restoreCache(): bool
    {
        if (config('Handlers')->cacheDuration === null) {
            return false;
        }

        if (null === $handlers = cache(static::cacheKey())) {
            return false;
        }

        self::$discovered[static::HANDLER_PATH] = $handlers;

        return true;
    }
}
