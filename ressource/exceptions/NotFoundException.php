<?php
namespace app\ressource\exceptions;

/**
 * Exception pour les erreurs 404
 *
 * @package app\ressource\form\input
 */
class NotFoundException extends \Exception {
	/**
	 * Créé une nouvelle exception HTTP pour une erreur 404
	 *
	 * @param string     $message   Le message d'erreur
	 * @param \Exception $previous  La précédente exception survenue
	 */
	public function __construct($message, \Exception $previous = null) {
		parent::__construct($message, 404, $previous);
	}
}