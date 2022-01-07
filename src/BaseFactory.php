<?php

namespace Tatter\Handlers;

use CodeIgniter\Cache\CacheInterface;
use Tatter\Handlers\Config\Handlers as HandlersConfig;
use Tatter\Handlers\Interfaces\HandlerInterface;
use Throwable;
use UnexpectedValueException;

abstract class BaseFactory
{
    /**
     * The configuration.
     *
     * @var HandlersConfig
     */
    protected $config;

    /**
     * The Cache handler instance.
     *
     * @var CacheInterface|null
     */
    protected $cache;

    /**
     * The cache key to use.
     *
     * @var string
     */
    protected $cacheKey;

    /**
     * Array of filters.
     *
     * @var array of [key, operator, value, combine]
     */
    protected $filters = [];

    /**
     * Array of discovered handler attributes,
     * indexed by their handlerId.
     *
     * @var array<string, array>
     */
    protected $discovered = [];

    /**
     * Returns the search path.
     */
    abstract public function getPath(): string;

    /**
     * Initializes the library.
     *
     * @throws UnexpectedValueException
     */
    final public function __construct(?HandlersConfig $config = null)
    {
        $this->config = $config ?? config('Handlers');

        $path = $this->getPath();
        if ($path === '' || strpos($path, DIRECTORY_SEPARATOR) !== false) {
            throw new UnexpectedValueException('Invalid path provided: ' . $path);
        }

        $this->cacheKey = 'handlers-' . mb_url_title($path, '-', true);

        $this->discoverHandlers();
    }

    /**
     * Returns the interface required for handlers to match.
     *
     * @return class-string<HandlerInterface>
     */
    public function getInterface(): string
    {
        return HandlerInterface::class;
    }

    /**
     * Returns the current configuration.
     */
    final public function getConfig(): HandlersConfig
    {
        return $this->config;
    }

    /**
     * Returns the attributes for a discovered class.
     */
    final public function getAttributes(string $handlerId): ?array
    {
        return $this->discovered[$handlerId] ?? null;
    }

    //--------------------------------------------------------------------

    /**
     * Adds attribute filters.
     *
     * @param array<string, mixed> $criteria
     *
     * @return $this
     */
    final public function where(array $criteria): self
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
    final public function orWhere(array $criteria): self
    {
        $this->parseCriteria($criteria, false);

        return $this;
    }

    /**
     * Resets filters between returns.
     *
     * @return $this
     */
    final public function reset(): self
    {
        $this->filters = [];

        return $this;
    }

    /**
     * Parses "where" $criteria and adds to $filters
     *
     * @param array $criteria Array of 'key [operator]' => 'value'
     * @param bool  $combine  Whether the resulting filter should be combined with others
     */
    private function parseCriteria(array $criteria, bool $combine): void
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

    //--------------------------------------------------------------------

    /**
     * Returns the first matched class. Short-circuits the namespace
     * traversal to optimize performance.
     *
     * @return class-string<HandlerInterface>|null The full class name, or null if none found
     */
    final public function first(): ?string
    {
        $class = $this->filterHandlers()[0] ?? null;
        $this->reset();

        return $class;
    }

    /**
     * Returns an array of all matched classes.
     *
     * @return class-string<HandlerInterface>[]
     */
    final public function findAll(): array
    {
        $classes = $this->filterHandlers();
        $this->reset();

        return $classes;
    }

    /**
     * Returns a handler by its handlerId. Ignores filters.
     *
     * @return class-string<HandlerInterface>|null The full class name, or null if none found
     */
    final public function find(string $handlerId): ?string
    {
        return $this->discovered[$handlerId]['class'] ?? null;
    }

    //--------------------------------------------------------------------

    /**
     * Filters discovered classes by the defined criteria.
     *
     * @param int|null $limit Limit on how many classes to match
     *
     * @throws UnexpectedValueException
     *
     * @return class-string<HandlerInterface>[]
     */
    final protected function filterHandlers(?int $limit = null): array
    {
        // Make sure there is work to do
        if ($this->discovered === []) {
            return [];
        }

        // If there are no filters then grab all classes
        if ($this->filters === []) {
            $classes = array_column($this->discovered, 'class');

            return $limit ? array_slice($classes, 0, $limit) : $classes;
        }

        $classes = [];

        foreach ($this->discovered as $handlerId => $attributes) {
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
                        $test = (string) $attributes[$key] === (string) $value;
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

                    // Requires the attribute to be a CSV
                    case 'has':
                        $test = in_array($value, explode(',', $attributes[$key]), true);
                    break;

                    default:
                        throw new UnexpectedValueException($operator . ' is not a valid criteria operator');
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
                $classes[] = $attributes['class'];

                if ($limit && count($classes) >= $limit) {
                    return $classes;
                }
            }
        }

        return $classes;
    }

    /**
     * Iterates through namespaces and finds HandlerInterfaces in the search path.
     */
    private function discoverHandlers(): void
    {
        // Check the cache first
        if ($this->restoreCache()) {
            return;
        }

        $path = $this->getPath();
        service('timer')->start('Discover ' . $path);

        // Have to do this the hard way
        $locator = service('locator');

        // Scan each namespace
        $this->discovered = [];

        foreach (service('autoloader')->getNamespace() as $namespace => $paths) {
            // Check for files in the path for this namespace
            foreach ($locator->listNamespaceFiles($namespace, $path) as $file) {
                // Try to get the class name
                if (! $class = $this->getHandlerClass($file, $namespace)) {
                    continue;
                }

                // Make sure it is not an ignored class
                if (in_array($class, $this->config->ignoredClasses, true)) {
                    continue;
                }

                // A match! Get the instance attributes
                $handlerId  = $class::handlerId();
                $attributes = $class::attributes();

                $attributes['id']    = $attributes['id'] ?? $handlerId;
                $attributes['class'] = $attributes['class'] ?? $class; // @phpstan-ignore-line

                $this->discovered[$handlerId] = $attributes;
            }
        }

        ksort($this->discovered, SORT_STRING);

        // Cache the results
        $this->commitCache();

        service('timer')->stop('Discover ' . $path);
    }

    /**
     * Validates that a file path contains a HandlerInterface
     * (or $this->getInterface()) and returns its full class name.
     *
     * @param string $file      Full path to the file in question
     * @param string $namespace The file's namespace
     *
     * @return string|null The fully-namespaced class
     */
    final public function getHandlerClass(string $file, string $namespace): ?string
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
        $class = $namespace . '\\' . $this->getPath() . '\\' . basename($file, '.php');

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
        if (! in_array($this->getInterface(), $interfaces, true)) {
            return null;
        }

        return $class;
    }

    //--------------------------------------------------------------------

    /**
     * Commits discovered classes to the cache. Called after discovery.
     */
    final protected function commitCache(): void
    {
        if ($this->config->cacheDuration !== null) {
            cache()->save($this->cacheKey, $this->discovered, $this->config->cacheDuration);
        }
    }

    /**
     * Loads any discovered classes from the cache.
     *
     * @return bool Whether there was cache content to restore
     */
    final protected function restoreCache(): bool
    {
        if ($this->config->cacheDuration === null) {
            return false;
        }

        if (null === $discovered = cache($this->cacheKey)) {
            return false;
        }

        $this->discovered = $discovered;

        return true;
    }

    /**
     * Removes discovered classes from the cache.
     *
     * @return $this
     */
    final public function clearCache(): self
    {
        cache()->delete($this->cacheKey);

        return $this;
    }
}
