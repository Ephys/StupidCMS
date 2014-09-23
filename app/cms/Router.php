<?php
namespace app\cms;

use app\helpers\DebugHelper;
use app\helpers\StringHelper;
use app\Kernel;
use Exception;
use InvalidArgumentException;

/**
 * Classe servant à appeler le bon contôleur et la bonne vue
 *
 * @package app\cms
 */
class Router {
	private $routes = [];
	private $errorRoutes = [];

	/** @var int le code d'erreur de la route si une erreur est survenue */
	private $activeErrorRoute;

	/** @var string le code de la route */
	private $activeRoute;

	/** @var callable le controleur appelé */
	private $controller;

	/** @var array les arguments pour le controleur */
	private $args;

	/** @var Response la réponse a envoyer */
	private $response;

	private $basepath;

	private $baseDir;

	public function __construct() {
		// Ajoute le contrôleur par défaut aux routes
		$this->errorRoutes['default'] = function (Exception $e) {
			if (Kernel::getKernel()->isDevMode())
				return '<p>Vous devriez setup une page d\'erreur avec <b>Router::addErrorRoute(' . $e->getCode() . ', $controller)</b>.
                        <br><br>Erreur ' . $e->getCode() . ' ' . $e->getMessage() . '
                        <br><br><b>Stack Trace</b><br>
                        <pre>' . DebugHelper::getStackTrace($e) . '</pre></p>';

			return '<p>Erreur ' . $e->getCode() . '</p>';
		};

		$this->response = new Response();
		$this->basepath = $this->generateBasePath();
	}

	/**
	 * Retourne le nom de la route active.
	 * Retournera null si la méthode est appelée avant Router::route()
	 *
	 * @return string|null
	 */
	public function getActiveRoute() {
		return $this->activeRoute;
	}

	/**
	 * Retourne le code d'erreur de la page ou null si aucune erreur n'est survenue
	 *
	 * @return int|null
	 */
	public function getActiveErrorNum() {
		return $this->activeErrorRoute;
	}

	/**
	 * Génère une url vers la route demandée avec certains paramètres
	 *
	 * @param string $routeName le nom de la route a générer
	 * @param array  $args      (optionnel) Les arguments de la route
	 * @throws \InvalidArgumentException invalid route
	 * @return string
	 */
	public function generateUrl($routeName, $args = []) {
		if (!isset($this->routes[$routeName]))
			throw new \InvalidArgumentException('Router::generateUrl: Invalid route "' . $routeName . '" Existing routes are: ' . StringHelper::toString($this->routes), 500);

		$i = 0;

		return preg_replace_callback('#{([a-zA-Z]+)}#', function () use ($args, &$i, $routeName) {
			if ($i >= count($args))
				throw new \LengthException('Router::generateUrl: Trop peu d\'arguments: ' . count($args) . ' donnés pour la route "' . $this->routes[$routeName][1] . '"', 500);

			return $args[$i++];
		}, $this->routes[$routeName][1]);
	}

	/**
	 * Extrapole le path des sous-dossiers dans lesquels le site est installé sur apache.
	 *
	 * @return string
	 */
	private function generateBasePath() {
		// dossier contenant "app", "src", et "web"
		$this->baseDir = $baseDir = realpath(dirname(__FILE__) . '/../../');

		// Windows only: on retire le slash final qui posera problème.
		$apacheDir = StringHelper::removeEndSlash($_SERVER['DOCUMENT_ROOT']);

		// document_root contient le dossier apache, $basedir contient le dossier du projet.
		// On soustrait document_root à basedir et on obtient l'url en cas de sous-dossier dans apache
		$basePath = strrev(substr(strrev($baseDir), 0, -(strlen($apacheDir)))) . '/';

		// Windows only: on inverse les \ en / pour respecter le format de l'url.
		return Kernel::isWindows() ? str_replace('\\', '/', $basePath) : $basePath;
	}

	/**
	 * Modifie le contrôleur pour le code d'erreur $errorCode.
	 *
	 * @param $errorCode    int|string: le code d'erreur ou 'default'.
	 * @param $controller   callable: fonction à appeler si cette erreur survient (format call_user_func_array)
	 */
	public function addErrorRoute($errorCode, $controller) {
		$this->errorRoutes[$errorCode] = $controller;
	}

	/**
	 * Modifie le contrôleur pour un URI donné au format /chemin/{variable}/
	 * où les éléments entre crochets sont des variables qui seront fournies au contrôleur.
	 *
	 * @param             $path          string l'URI
	 * @param             $controller    callable: Une fonction à appeler (format call_user_func_array)
	 * @param string|null $name          [optionnel] Le nom de la route, si null; sera déduit du $path
	 * @throws \InvalidArgumentException Le nom de route est déjà utilisé
	 */
	public function addRoute($path, $controller, $name = null) {
		$path = StringHelper::removeStartSlash($path);
		$path = StringHelper::removeEndSlash($path);

		if ($name === null)
			$name = StringHelper::makeAlphanumeric($path);

		if (!empty($this->routes[$name])) {
			throw new InvalidArgumentException('Nom de route "' . $name . '" déjà utilisé par la route "' . $this->routes[$name][1] . '" lors de l\'essai d\'ajout de "' . $path . '"');
		}

		$this->routes[$name] = [0 => $controller, 1 => $path];
	}

	/**
	 * Exécute le contrôleur de l'erreur fournie en code d'exception. Exécute le contrôleur par défaut si aucun n'est disponible
	 *
	 * @param \Exception $e
	 */
	private function error(Exception $e) {
		$num = $e->getCode();

		if (isset($this->errorRoutes[$num]))
			$controller = $this->errorRoutes[$num];
		else
			$controller = $this->errorRoutes['default'];

		$this->response->clearHeaders();
		$this->response->setResponseCode($num);

		$this->activeErrorRoute = $num;
		$this->call($controller, [$e]);
	}

	/**
	 * Exécute le contrôleur correspondant à l'URI de la page. Ne devrait être exécuté qu'après la déclaration
	 * de toutes les routes avec Router::addRoute()
	 *
	 * En cas d'erreur, appelle le contrôleur correspondant au code d'erreur survenu
	 */
	public function route() {
		if (empty($_SERVER['REDIRECT_URL'])) {
			$this->error(new Exception('La redirection ne s\'est pas déroulée correctement. Soyez sur d\'avoir mod_rewrite (rewrite module) disponible et actif sur votre configuration Apache2 (version 2.2.22 recommendée)', 500));

			return;
		}

		$this->fetchController();

		if ($this->controller === null) {
			$this->error(new Exception('Route not found', 404));

			return;
		}

		try {
			$this->call($this->controller, $this->args);
		} catch (Exception $e) {
			$this->error($e);
		}
	}

	private function call($method, $args) {
		/** @link http://be2.php.net/manual/en/function.call-user-func-array.php */
		$output = call_user_func_array($method, $args);

		/*
		 * Si l'output renvoie un string: c'était un modèle et on affiche ce string.
		 * S'il retrourne un array: c'était un contrôleur et on appelle le modèle.
		 */

		if ($output instanceof Response)
			$this->response = $output;
		else if (is_string($output)) {
			$this->response->setResponseText($output);
		}
		else if (is_array($output)) {
			if (!is_string($method))
				throw new Exception('Cannot guess view from a function. Please use a method.', 500);

			$modelMethod = str_replace(['controller', 'Controller'], ['view', 'View'], $method);

			$this->response->setResponseText(call_user_func_array($modelMethod, $output));
		}
		else {
			throw new Exception('Wrong output type : should return either an instance of app\cms\Response, a string or an array; ' . gettype($output) . ' received.', 500);
		}

		$this->response->send();
	}

	/**
	 * Retourne l'url racine (pour <base>)
	 *
	 * @return string
	 */
	public function getBasePath() {
		return $this->basepath;
	}

	/**
	 * Retourne le chemin absolu vers le dossier racine
	 *
	 * @return string
	 */
	public function getBaseDir() {
		return $this->baseDir;
	}

	/**
	 * Récupère le contrôleur correspondant à l'URI de la page,
	 * le stocke dans $this->controller
	 * et enregistre ses paramètres dans $this->args
	 */
	private function fetchController() {
		// ========= Etape 1: récupérer le basepath dynamiquement
		// Le seul intérêt de ce bout de code est de pouvoir mettre le CMS dans un sous-dossier sans poser de problème
		// donc de remplacer "/projetPhp/home" par "/home" :)

		// $_SERVER['REDIRECT_URL'] contient l'url demandée par l'utilisateur (exemple: /projetPhp/home/test/hello)
		// $_SERVER['SCRIPT_NAME']  contient le chemin absolu vers ce fichier (exemple: /home/ephys/projetPhp/Router.php)
		// $requestUri contiendra '/home/test/hello'
		$len        = strlen(dirname($_SERVER['SCRIPT_NAME']));
		$requestUri = substr($_SERVER['REDIRECT_URL'], $len, strlen($_SERVER['REDIRECT_URL']) - $len);

		// On retire les slashs de début et de fin pour pas perturber "explode", $requestUri contiendra 'home/test/hello'
		$requestUri = StringHelper::removeStartSlash($requestUri);
		$requestUri = StringHelper::removeEndSlash($requestUri);

		// ========= Etape 2: Comparer l'url demandée et la route

		// Principe:
		// pour chaque route enregistrée, on transforme le chemin /home/hello/test en un tableau
		// ['home', 'hello', 'test'] et on compare chaque élément avec l'URI demandée.
		// Si ils sont identiques, c'est que c'est la bonne route et on prend son controlleur

		// Cas particulier, les éléments du tableau qui commencent par { et finissent par }. Ils ne sont pas comparés
		// à la place on récupère sa valeur dans l'URI et ils seront envoyés au controlleur !

		$requestUri = explode('/', $requestUri);

		$this->controller = null;

		foreach ($this->routes as $routeName => $routeData) {
			$controller = $routeData[0];
			$route      = $routeData[1];

			$route = explode('/', $route);

			if (count($route) !== count($requestUri))
				continue;

			$data    = [];
			$isValid = false;
			for ($i = 0; $i < count($route); $i++) {
				if (isset($route[$i][0]) && $route[$i][0] === '{' && $route[$i][strlen($route[$i]) - 1] === '}') {
					$data[substr($route[$i], 1, strlen($route[$i]) - 2)] = $requestUri[$i];
					$isValid                                             = true;
				}
				else if ($route[$i] !== $requestUri[$i]) {
					$isValid = false;
					break;
				}
				else {
					$isValid = true;
				}
			}

			if ($isValid) {
				$this->controller  = $controller;
				$this->args        = $data;
				$this->activeRoute = $routeName;

				return;
			}
		}
	}
}