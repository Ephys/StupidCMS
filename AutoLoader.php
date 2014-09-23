<?php
namespace app;
use Exception;

/**
 * Dynamically loads project classes
 *
 * @package app
 */
class AutoLoader {
	private $paths = [];

	public function __construct() {
		$this->register();
	}

	/**
	 * Adds a namespace to the classpath.
	 *
	 * @param   string $namespace   The namespace
	 * @param   string $path        The path this namespace is referring to
	 * @return  $this
	 */
	public function add($namespace, $path) {
		$this->paths[$namespace] = $path;

		return $this;
	}

	/**
	 * Looks up the filepath for a given class
	 *
	 * @param   string $clazz The name of a class, namespace included
	 * @return  string|null
	 */
	private function getPath($clazz) {
		foreach ($this->paths as $namespace => $path) {
			if (substr($clazz, 0, strlen($namespace)) === $namespace)
				return $path;
		}

		return null;
	}

	/**
	 * Registers this autoloader to the autoload stack
	 */
	private function register() {
		/** @link http://be2.php.net/manual/en/function.spl-autoload-register.php */
		spl_autoload_register(function ($clazz) {
			/** @var String $clazzPath filepath to include */
			$clazzPath = $this->getPath($clazz);

			if ($clazzPath === null)
				return;

			$clazzPath = $clazzPath . '/' . str_replace('\\', '/', $clazz) . '.php';

			if (!is_file($clazzPath))
				throw new Exception('File Not Found: ' . $clazzPath, 500);

			include_once $clazzPath;
		});
	}
}

return new AutoLoader();