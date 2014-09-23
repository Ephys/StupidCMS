<?php
namespace app\cms\view;

use app\Kernel;

/**
 * Classe contenant des méthodes métiers pour les vues.
 *
 * @package app\cms\view
 */
class BaseView {
	/**
	 * Retourne l'url racine du site, utilisé pour la balise <base>
	 *
	 * @return string
	 */
	protected static function getBasePath() {
		return 'http://' . $_SERVER['HTTP_HOST'] . Kernel::getKernel()->getRouter()->getBasePath();
	}

	/**
	 * Génère une url vers la route demandée avec certains paramètres
	 *
	 * @param string            $routeName le nom de la route a générer
	 * @param array (optionnel) $args      Les arguments de la route
	 * @return string
	 */
	protected static function generateUrl($routeName, $args = null) {
		return self::getBasePath() . Kernel::getKernel()->getRouter()->generateUrl($routeName, $args);
	}

	/**
	 * Retourne le nom de la route active
	 *
	 * @return string
	 */
	protected static function getRoute() {
		return Kernel::getKernel()->getRouter()->getActiveRoute();
	}
} 