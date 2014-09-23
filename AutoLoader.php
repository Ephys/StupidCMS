<?php
namespace app;

/**
 * Charge dynamiquement les classes PHP du projet
 *
 * @package app
 */
class AutoLoader {
	private $paths = [];
	private static $autoLoader;

	private final function __construct() {
	}

	private final function __clone() {
	}

	/**
	 * Retourne l'instance de l'autoloader, la créé si elle n'existe pas.
	 * À appeller à la place du constructeur puisqu'il ne peut y avoir qu'une seule instance (singleton)
	 *
	 * @return AutoLoader
	 */
	public final static function getInstance() {
		if (self::$autoLoader === null) {
			self::$autoLoader = new AutoLoader();
			self::$autoLoader->register();
		}

		return self::$autoLoader;
	}

	/**
	 * Ajoute un namespace à la liste et enregistre le chemin vers le dossier y correspondant
	 *
	 * @param   string $namespace
	 * @param   string $path
	 * @return  $this
	 */
	public function add($namespace, $path) {
		$this->paths[$namespace] = $path;

		return $this;
	}

	/**
	 * Retourne le dossier correspondant au namespace d'une classe
	 *
	 * @param   string $clazz le nom d'une classe (namespace compris)
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
	 * Enregistre l'autoloader au registre de PHP, voir fonction native "spl_autoload_register"
	 */
	private function register() {
		/** @link http://be2.php.net/manual/en/function.spl-autoload-register.php */
		spl_autoload_register(function ($clazz) {
			//if (class_exists($clazz)) return;
			$clazzPath = $this->getPath($clazz);

			if ($clazzPath === null)
				return;

			// remplace les antislashes d'un namespace par des slashs.
			// app\cms\Router deviens app/cms/Router puis /home/ephys/projetPhp/app/cms/Router.php
			// Fichier qui sera inclus.
			$clazzPath = $clazzPath . '/' . str_replace('\\', '/', $clazz) . '.php';

			if (!is_file($clazzPath))
				throw new \Exception('File Not Found: ' . $clazzPath, 500);

			include_once $clazzPath;
		});
	}
}

return AutoLoader::getInstance();