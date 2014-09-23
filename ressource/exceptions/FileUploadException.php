<?php
namespace app\ressource\exceptions;

/**
 * Exception pour les uploads de fichiers
 *
 * @package app\ressource\form\input
 */
class FileUploadException extends \Exception {
	/**
	 * Créé une nouvelle exception pour les uploads de fichiers
	 *
	 * @param string     $message   Le message d'erreur
	 * @param int        $errno     Le code d'erreur
	 * @param \Exception $previous  La précédente exception survenue
	 */
	public function __construct($message, $errno = 500, \Exception $previous = null) {
		parent::__construct($message, $errno, $previous);
	}
}