<?php
namespace app;

use app\cms\Router;
use app\ressource\Configuration;
use app\ressource\Database;
use PDO;

/**
 * Sert de wrapper aux différentes classes d'un projet
 *
 * @package app
 */
abstract class Kernel {
	/** @var Kernel l'instance du kernel */
	private static $kernel;

	/** @var AutoLoader l'instance de l'autoloader du projet */
	private $autoloader = null;

	/**  @var Router l'instance du routeur du projet */
	private $router = null;

	/** @var bool mode développeur */
	private $devMode = false;

	/** @var Configuration le gestionnaire de fichier de configuration du projet */
	private $config = null;

	/** @var Database le gestionnaire de connexions PDO */
	protected $database = null;

	/** @var string[] system locales filenames */
	protected $locales = [];

	/**
	 * Créé un nouveau kernel
	 *
	 * @param boolean    $isDevMode     Mode développement ou mode production
	 * @param AutoLoader $autoloader    Le loader de classes du projet
	 * @throws \Exception               Un kernel existe déjà
	 */
	public function __construct($isDevMode, AutoLoader $autoloader) {
		$this->setDevMode($isDevMode);

		if (self::$kernel !== null)
			throw new \Exception('Kernel is already running.');

		self::$kernel = $this;

		define(__NAMESPACE__ . '\\IS_WINDOWS', strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');

		$this->autoloader = $autoloader;
		$this->registerNamespaces($this->autoloader);

		ErrorManager::register();

		$this->router = new Router();
		$this->registerRoutes($this->router);

		$this->database = new Database();

		session_start();
	}

	/**
	 * Renvoie l'instance de PDO correspondant au nom donné en paramètre
	 *
	 * @param mixed $dbname Le nom de l'handler de la database
	 * @return PDO
	 */
	public function getPDO($dbname = 'default') {
		return $this->database->getInstance($dbname);
	}

	/**
	 * Renvoie le kernel actif.
	 *
	 * @return Kernel
	 */
	public static function getKernel() {
		return self::$kernel;
	}

	public function getLocale($lang) {
		return isset($this->locales[$lang]) ? $this->locales[$lang] : null;
	}

	/**
	 * Définis si le système d'exploitation utilisé est windows
	 * @return boolean
	 */
	public static final function isWindows() {
		return constant(__NAMESPACE__ . '\\IS_WINDOWS');
	}

	/**
	 * Récupère la classe gérant la configuration du projet
	 *
	 * @return Configuration
	 */
	public function getConfig() {
		return $this->config;
	}

	/**
	 * Change le mode d'exécution de l'application
	 *
	 * @param $isDevMode
	 */
	public function setDevMode($isDevMode) {
		$this->devMode = (bool)$isDevMode;

		if ($this->devMode)
			ini_set('display_errors', 'on');
		else
			ini_set('display_errors', 'off');
	}

	/**
	 * Récupère le mode d'exécution de l'application
	 *
	 * @return boolean
	 */
	public function isDevMode() {
		return $this->devMode;
	}

	/**
	 * Renvoie l'instance du routeur du projet
	 *
	 * @return Router;
	 */
	public function getRouter() {
		return $this->router;
	}

	/**
	 * Initiates the page generation scripts
	 */
	public function run() {
		$this->router->route();
	}

	/**
	 * Définis quel fichier sert de configuration au projet
	 *
	 * @param string $filename
	 */
	protected function setConfigFile($filename) {
		$this->config = new Configuration($filename);
	}

	/**
	 * Permet d'ajouter les dossiers relatifs aux namespaces du projet
	 *
	 * @param AutoLoader $loader
	 */
	protected abstract function registerNamespaces(AutoLoader $loader);

	/**
	 * Permet d'ajouter des routes au projet
	 *
	 * @param Router $router
	 */
	protected abstract function registerRoutes(Router $router);

	/**
	 * Renvoie l'instance du loader de classes dynamique du projet
	 *
	 * @return AutoLoader
	 */
	protected function getAutoLoader() {
		return $this->autoloader;
	}
}