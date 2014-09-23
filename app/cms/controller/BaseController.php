<?php
namespace app\cms\controller;

use app\cms\RedirectionResponse;
use app\cms\view\BaseView;
use app\Kernel;
use Exception;
use PDOException;

/**
 * Classe contenant des méthodes métiers pour les contrôleur.
 *
 * @package app\cms\controller
 */
abstract class BaseController extends BaseView {
	/**
	 * Retourne le gestionnaire de base de donnée. Cette méthode est à préférer à Database::getInstance()
	 * parce qu'il gère les erreurs HTTP.
	 *
	 * @param string $name Le nom de l'instance PDO
	 * @throws \Exception : Erreur 500 - une PDOException a été lancée
	 * @return \PDO
	 */
	protected static function getPDO($name = 'default') {
		try {
			return Kernel::getKernel()->getPDO($name);
		} catch (PDOException $e) {
			throw new Exception($e->getMessage(), 500);
		}
	}

	/**
	 * Créé une nouvelle réponse provoquant une redirection vers $url
	 *
	 * @param string $url L'url de redirection
	 * @return RedirectionResponse  La réponse à renvoyer au routeur
	 */
	protected static function redirection($url) {
		return new RedirectionResponse($url);
	}
}