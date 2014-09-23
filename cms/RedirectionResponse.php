<?php
namespace app\cms;

/**
 * Classe servant de stockage pour une réponse HTTP 303
 *
 * @package app\cms
 */
class RedirectionResponse extends Response {
	/**
	 * Créé une nouvelle réponse prévue pour la redirection
	 * @param string $url L'url vers laquelle la réponse va rediriger
	 */
	public function __construct($url) {
		parent::__construct('');

		$this->setResponseCode(303);

		$this->addHeader('Location: ' . $url);
	}
} 