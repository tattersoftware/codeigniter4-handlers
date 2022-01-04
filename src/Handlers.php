<?php

namespace Tatter\Handlers;

use CodeIgniter\Cache\CacheInterface;
use Config\Services;
use Tatter\Handlers\Config\Handlers as HandlersConfig;
use Tatter\Handlers\Interfaces\HandlerInterface;

class Handlers
{
    /**
     * Path to search across namespaces.
     *
     * @var string
     */
    protected $path;

    /**
     * The configuration.
     *
     * @var HandlersConfig
     */
    protected $config;

    /**
     * The Cache handler instance.
     *
     * @var CacheInterface
     */
    protected $cache;

    /**
     * Array of filters.
     *
     * @var array of [key, operator, value, combine]
     */
    protected $filters = [];

    /**
     * Array of discovered HandlerInterface class names and their attributes.
     *
     * @var array<string, array>|null
     */
    protected $discovered;

    /**
     * Initializes the library.
     */
    public function __construct(string $path = '', ?HandlersConfig $config = null, ?CacheInterface $cache = null)
    {
        $this->path   = $path;
        $this->config = $config ?? config('Handlers');
        $this->cache  = $cache ?? service('cache');
    }

    /**
     * Returns the current configuration.
     */
    public function getConfig(): HandlersConfig
    {
        return $this->config;
    }

    /**
     * Returns the search path.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Sets the search path and resets discovery.
     *
     * @return $this
     */
    public function setPath(string $path): self
    {
        if ($path !== $this->path) {
            $this->path       = $path;
            $this->discovered = null;
        }

        return $this;
    }

    /**
     * Returns the attributes for a discovered class.
     */
    public function getAttributes(string $class): ?array
    {
        $this->discoverHandlers();

        return $this->discovered[$class] ?? null;
    }

    //--------------------------------------------------------------------

    /**
     * Adds attribute filters.
     *
     * @param array<string, mixed> $criteria
     *
     * @return $this
     */
    public function where(array $criteria): self
    {
        $this->parseCriteria($criteria, true);

        return $this;
    }

    /**
     * Adds attribute filters that do not combine.
     *
     * @param array<string, mixed> $criteria
     *
     * @return $this
     */
    public function orWhere(array $criteria): self
    {
        $this->parseCriteria($criteria, false);

        return $this;
    }

    /**
     * Resets filters between returns.
     *
     * @return $this
     */
    public function reset(): self
    {
        $this->filters = [];

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Returns the first matched class. Short-circuits the namespace
     * traversal to optimize performance.
     *
     * @return string|null The full class name, or null if none found
     */
    public function first(): ?string
    {
        $class = $this->filterHandlers()[0] ?? null;
        $this->reset();

        return $class;
    }

    /**
     * Returns an array of all matched classes.
     *
     * @return array<string>
     */
    public function findAll(): array
    {
        $classes = $this->filterHandlers();
        $this->reset();

        return $classes;
    }

    /**
     * Returns a handler with a given name. Ignores filters.
     * Searches: attribute "name" or "uid", namespaced class, and short class name.
     *
     * @param string $name The name of the handler
     *
     * @return string|null The full class name, or null if none found
     */
    public function find(string $name): ?string
    {
        $this->discoverHandlers();

        $name = trim($name, '\\ ');

        foreach ($this->discovered as $class => $attributes) {
            // Check the namespaced class
            if ($class === $name) {
                return $class;
            }

            // Check the attributes
            if (isset($attributes['name']) && $attributes['name'] === $name) {
                return $class;
            }
            if (isset($attributes['uid']) && $attributes['uid'] === $name) {
                return $class;
            }

            // Check the class shortname
            if ($pos = strrpos($class, '\\')) {
                if (substr($class, $pos + 1) === $name) {
                    return $class;
                }
            }
        }

        return null;
    }

    //--------------------------------------------------------------------

    /**
     * Returns an array of all matched classes.
     *
     * @return array<string>
     *
     * @deprecated Use findAll()
     */
    public function all(): array
    {
        return $this->findAll();
    }

    /**
     * Returns a handler with a given name. Ignores filters.
     * Searches: attribute "name" or "uid", namespaced class, and short class name.
     *
     * @param string $name The name of the handler
     *
     * @return string|null The full class name, or null if none found
     *
     * @deprecated Use find()
     */
    public function named(string $name): ?string
    {
        return $this->find($name);
    }

    //--------------------------------------------------------------------

    /**
     * Parses "where" $criteria and adds to $filters
     *
     * @param array $criteria Array of 'key [operator]' => 'value'
     * @param bool  $combine  Whether the resulting filter should be combined with others
     */
    protected function parseCriteria(array $criteria, bool $combine): void
    {
        foreach ($criteria as $key => $value) {
            // Check for an operator
            $key = trim($key);
            if (strpos($key, ' ')) {
                [$key, $operator] = explode(' ', $key);
            } else {
                $operator = '==';
            }

            $this->filters[] = [
                $key,
                $operator,
                $value,
                $combine,
            ];
        }
    }

    /**
     * Iterates through discovered handlers and attempts to register them.
     *
     * @return array<string> Array of newly-registered classes
     */
    public function register(): array
    {
        $this->discoverHandlers();

        if (empty($this->discovered)) {
            return [];
        }

        service('timer')->start('Register ' . $this->path);

        $classes = [];

        foreach ($this->discovered as $class => $attributes) {
            $instance = new $class();

            if (is_callable([$instance, 'register']) && $instance->register()) {
                $classes[] = $class;
            }
        }

        service('timer')->stop('Discover ' . $this->path);

        return $classes;
    }

    //--------------------------------------------------------------------

    /**
     * Filters discovered classes by the defined criteria.
     *
     * @param int|null $limit Limit on how many classes to match
     *
     * @throws \RuntimeException
     *
     * @return array<string>
     */
    protected function filterHandlers(?int $limit = null): array
    {
        $this->discoverHandlers();

        // Make sure there is work to do
        if (empty($this->filters) || empty($this->discovered)) {
            $classes = array_keys($this->discovered);

            return $limit ? array_slice($classes, 0, $limit) : $classes;
        }

        $classes = [];

        foreach ($this->discovered as $class => $attributes) {
            $result = true;

            // Check each attribute against the filters
            foreach ($this->filters as $filter) {
                // Split out the array to make it easier to read
                [$key, $operator, $value, $combine] = $filter;

                if (! isset($attributes[$key])) {
                    $result = false;

                    continue;
                }

                switch ($operator) {
                    case '==':
                    case '=':
                        $test = $attributes[$key] === $value;
                    break;

                    case '===':
                        $test = $attributes[$key] === $value;
                    break;

                    case '>':
                        $test = $attributes[$key] > $value;
                    break;

                    case '>=':
                        $test = $attributes[$key] >= $value;
                    break;

                    case '<':
                        $test = $attributes[$key] < $value;
                    break;

                    case '<=':
                        $test = $attributes[$key] <= $value;
                    break;

                    // Assumes the attribute is a CSV
                    case 'has':
                        $test = in_array($value, explode(',', $attributes[$key]), true);
                    break;

                    default:
                        throw new \RuntimeException($operator . ' is not a vald criteria operator');
                }

                // If this filter was sufficient on its own then skip the rest of the filters
                if ($test && ! $combine) {
                    $result = true;
                    break;
                }

                $result = $result && $test;
            }

            // Check for a match
            if ($result) {
                $classes[] = $class;

                if ($limit && count($classes) >= $limit) {
                    return $classes;
                }
            }
        }

        return $classes;
    }

    /**
     * Iterates through namespaces and finds HandlerInterfaces in $this->path.
     *
     * @return $this
     */
    protected function discoverHandlers(): self
    {
        if ($this->discovered !== null) {
            return $this;
        }

        // Check the cache first
        $this->cacheRestore();

        if ($this->discovered !== null) {
            return $this;
        }

        service('timer')->start('Discover ' . $this->path);

        // Have to do this the hard way
        $locator = Services::locator();

        // Scan each namespace
        $this->discovered = [];

        foreach (Services::autoloader()->getNamespace() as $namespace => $paths) {
            // Check for files in $this->path for this namespace
            foreach ($locator->listNamespaceFiles($namespace, $this->path) as $file) {
                // Try to get the class name
                if (! $class = $this->getHandlerClass($file, $namespace)) {
                    continue;
                }

                // Make sure it is not an ignored class
                if (in_array($class, $this->config->ignoredClasses, true)) {
                    continue;
                }

                // A match! Get the instance attributes
                $attributes = (new $class())->toArray();

                $this->discovered[$class] = $attributes;
            }
        }

        // Cache the results
        $this->cacheCommit();

        service('timer')->stop('Discover ' . $this->path);

        return $this;
    }

    /**
     * Validates that a file path contains a HandlerInterface and
     * returns its full class name.
     *
     * @param string $file      Full path to the file in question
     * @param string $namespace The file's namespace
     *
     * @return string|null The fully-namespaced class
     */
    public function getHandlerClass(string $file, string $namespace): ?string
    {
        // Skip non-PHP files
        if (substr($file, -4) !== '.php') {
            return null;
        }

        // Try to load the file
        try {
            include_once $file;
        } catch (\Throwable $e) {
            return null;
        }

        // Build the fully-namespaced class
        $class = $namespace . '\\' . $this->path . '\\' . basename($file, '.php');

        // Verify that the class is available
        if (! class_exists($class, false)) {
            return null;
        }

        // Verify the HandlerInterface
        if (! $interfaces = class_implements($class)) {
            return null;
        }

        if (! in_array(HandlerInterface::class, $interfaces, true)) {
            return null;
        }

        return $class;
    }

    //--------------------------------------------------------------------

    /**
     * Returns a standardized caching key for the current path.
     */
    protected function cacheKey(): string
    {
        return 'handlers-' . mb_url_title($this->path, '-', true);
    }

    /**
     * Commits discovered classes to the cache. Usually called by discoverHandlers().
     *
     * @return $this
     */
    protected function cacheCommit(): self
    {
        if ($this->config->cacheDuration !== null) {
            $this->cache->save($this->cacheKey(), $this->discovered, $this->config->cacheDuration);
        }

        return $this;
    }

    /**
     * Loads any discovered classes from the cache.
     *
     * @return $this
     */
    protected function cacheRestore(): self
    {
        if ($this->config->cacheDuration !== null) {
            $this->discovered = $this->cache->get($this->cacheKey());
        }

        return $this;
    }

    /**
     * Removes discovered classes from the cache.
     *
     * @return $this
     */
    public function cacheClear(): self
    {
        $this->cache->delete($this->cacheKey());

        return $this;
    }
}
