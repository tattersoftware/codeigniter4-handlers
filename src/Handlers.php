<?php namespace Tatter\Handlers;

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
	 * Array of attribute criteria.
	 *
	 * @var array<string, mixed>
	 */
	protected $criteria = [];

	/**
	 * Array of discovered HandlerInterface class names and their attributes.
	 *
	 * @var array<string, array>|null
	 */
	protected $discovered;

	/**
	 * Initializes the library.
	 *
	 * @param string $path
	 * @param HandlersConfig|null $config
	 * @param CacheInterface|null $cache
	 */
	public function __construct(string $path = '', HandlersConfig $config = null, CacheInterface $cache = null)
	{
		$this->path   = $path;
		$this->config = $config ?? config('Handlers');
		$this->cache  = $cache ?? service('cache');
	}

	/**
	 * Returns the curent configuration.
	 *
	 * @return HandlersConfig
	 */
	public function getConfig(): HandlersConfig
	{
		return $this->config;
	}

	/**
	 * Returns the search path.
	 *
	 * @return string
	 */
	public function getPath(): string
	{
		return $this->path;
	}

	/**
	 * Sets the search path and resets discovery.
	 *
	 * @param string $path
	 *
	 * @return $this
	 */
	public function setPath(string $path): self
	{
		if ($path !== $this->path)
		{
			$this->path       = $path;
			$this->discovered = null;
		}

		return $this;
	}

	/**
	 * Adds attribute criteria.
	 *
	 * @param array<string, mixed> $criteria
	 *
	 * @return $this
	 */
	public function where(array $criteria): self
	{
		$this->criteria = array_merge($this->criteria, $criteria);

		return $this;
	}

	/**
	 * Resets criteria between returns.
	 *
	 * @return $this
	 */
	public function reset(): self
	{
		$this->criteria = [];

		return $this;
	}

	//--------------------------------------------------------------------

	/**
	 * Returns the first matched class. Short-circuits the namespace
	 * traversal to optimize performance.
	 *
	 * @return string|null  The full class name, or null if none found
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
	public function all(): array
	{
		$classes = $this->filterHandlers();
		$this->reset();
		return $classes;
	}

	//--------------------------------------------------------------------

	/**
	 * Filters discovered classes by the criteria.
	 *
	 * @param int|null $limit  Limit on how many classes to match
	 *
	 * @return array<string>
	 */
	protected function filterHandlers(int $limit = null): array
	{
		$this->discoverHandlers();

		// Make sure there is work to do
		if (empty($this->criteria) || empty($this->discovered))
		{
			$classes = array_keys($this->discovered);

			return $limit ? array_slice($classes, 0, $limit) : $classes;
		}

		$classes = [];
		foreach ($this->discovered as $class => $attributes)
		{
			// Check each attribute against the criteria
			foreach ($this->criteria as $key => $value)
			{
				if ($attributes[$key] !== $value)
				{
					continue 2;
				}
			}

			// A match!
			$classes[] = $class;

			if ($limit && count($classes) >= $limit)
			{
				return $classes;
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
		if ($this->discovered !== null)
		{
			return $this;
		}

		// Check the cache first
		$this->cacheRestore();		
		if ($this->discovered !== null)
		{
			return $this;
		}

		// Have to do this the hard way
		$locator = Services::locator();

		// Scan each namespace
		$this->discovered = [];
		foreach (Services::autoloader()->getNamespace() as $namespace => $paths)
		{
			// Check for files in $this->path for this namespace
			foreach ($locator->listNamespaceFiles($namespace, $this->path) as $file)
			{
				// Try to get the class name
				if (! $class = $this->getHandlerClass($file, $namespace))
				{
					continue;
				}

				// Make sure it is not an ignored class
				if (in_array($class, $this->config->ignoredClasses))
				{
					continue;
				}
				
				// A match! Get the instance attributes
				$attributes = (new $class())->toArray();

				$this->discovered[$class] = $attributes;
			}
		}

		// Cache the results
		$this->cacheCommit();

		return $this;
	}

	/**
	 * Validates that a file path contains a HandlerInterface and
	 * returns its full class name.
	 *
	 * @param string $file  Full path to the file in question
	 * @param string $namespace  The file's namespace
	 *
	 * @return string|null  The fully-namespaced class
	 */
	public function getHandlerClass(string $file, string $namespace): ?string
	{
		// Skip non-PHP files
		if (substr($file, -4) !== '.php')
		{
			return null;
		}

		// Try to load the file
		try
		{
			include_once $file;
		}
		catch (\Throwable $e)
		{
			return null;
		}

		// Build the fully-namespaced class
		$class = $namespace . '\\' . $this->path . '\\' . basename($file, '.php');

		// Verify that the class is available
		if (! class_exists($class, false))
		{
			return null;
		}

		// Verify the HandlerInterface
		if (! $interfaces = class_implements($class))
		{
			return null;
		}

		if (! in_array(HandlerInterface::class, $interfaces))
		{
			return null;
		}

		return $class;
	}

	//--------------------------------------------------------------------

	/**
	 * Returns a standardized caching key for the current path.
	 *
	 * @return string
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
		if ($this->config->cacheDuration !== null)
		{
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
		if ($this->config->cacheDuration !== null)
		{
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
