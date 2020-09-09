<?php namespace Tatter\Handlers;

use Config\Services;
use Tatter\Handlers\Config\Handlers as HandlersConfig;
use Tatter\Handlers\Interfaces\HandlerInterface;

class Handlers
{
	/**
	 * The configuration.
	 *
	 * @var HandlersConfig
	 */
	protected $config;
	
	/**
	 * Path to search across namespaces.
	 *
	 * @var string
	 */
	protected $path;
	
	/**
	 * Array of attribute filter criteria.
	 *
	 * @var array<string, mixed>
	 */
	protected $filters = [];
	
	/**
	 * Array of discovered HandlerInterface class names.
	 *
	 * @var array<string>
	 */
	protected $discovered = [];

	/**
	 * Initializes the library.
	 *
	 * @param string $path
	 * @param HandlersConfig|null $config
	 */
	public function __construct(string $path = '', HandlersConfig $config = null)
	{		
		// Save the configuration
		$this->config = $config ?? config('Handlers');
		$this->path   = $path;
	}

	/**
	 * Gets the search path.
	 *
	 * @return string
	 */
	public function getPath(): string
	{
		return $this->path;
	}

	/**
	 * Sets the search path.
	 *
	 * @param string $path
	 *
	 * @return $this
	 */
	public function setPath(string $path): self
	{
		$this->path = $path;

		return $this;
	}

	/**
	 * Adds attribute filter criteria.
	 *
	 * @param array<string, mixed> $filters
	 *
	 * @return $this
	 */
	public function where(array $filters): self
	{
		$this->filters = array_merge($this->filters, $filters);

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
		$this->discoverHandlers(1);

		return $this->discovered[0] ?? null;
	}

	/**
	 * Returns an array of all matched classes.
	 */
	public function all(): array
	{
		return $this->discoverHandlers()->discovered;
	}

	/**
	 * Iterate through namespaces and find HandlerInterfaces
	 * in $this->path that match the filter criteria.
	 *
	 * @param int|null $limit  Limit on how many classes to discover before quitting
	 *
	 * @return $this
	 */
	protected function discoverHandlers(int $limit = null): self
	{
		$locator = Services::locator();

		// Scan each namespace
		foreach (Services::autoloader()->getNamespace() as $namespace => $paths)
		{
			// Check for files in $this->path for this namespace
			foreach ($locator->listNamespaceFiles($namespace, $this->path) as $file)
			{
				// Try to get the HandlerInterface class name
				if (! $class = $this->getHandlerClass($file, $namespace))
				{
					continue;
				}

				// Make sure it is not an ignore class
				if (in_array($class, $this->config->ignoredClasses))
				{
					continue;
				}

				// Check for filters
				if ($this->filters)
				{
					// Get the instance
					$instance = new $class();

					// Check each attribute against the filters
					foreach ($this->filters as $key => $value)
					{
						if ($instance->$key !== $value)
						{
							continue 2;
						}
					}
				}

				// A match!
				$this->discovered[] = $class;

				if ($limit && count($this->discovered) >= $limit)
				{
					return $this;
				}
			}
		}

		return $this;
	}

	//--------------------------------------------------------------------

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

	/**
	 * Resets the class between returns.
	 *
	 * @return $this
	 */
	public function reset(): self
	{
		$this->filters    = [];
		$this->discovered = [];

		return $this;
	}
}
