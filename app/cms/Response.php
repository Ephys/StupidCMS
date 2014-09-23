<?php
namespace app\cms;

/**
 * Classe servant de stockage pour une réponse HTTP
 *
 * @package app\cms
 */
class Response {
	/** @var array */
	private $headers;

	/** @var string */
	private $responseText;

	/** @var integer */
	private $responseCode;

	public function __construct($responseText = '') {
		$this->responseText = $responseText;

		$this->headers = [];
	}

	public function __toString() {
		return $this->getResponseText();
	}

	/**
	 * Change le code d'erreur de la page
	 *
	 * @param int $responseCode Un code erreur http valide
	 */
	public function setResponseCode($responseCode) {
		$this->responseCode = (int)$responseCode;
	}

	/**
	 * @return int le code d'erreur actuel de la page
	 */
	public function getResponseCode() {
		return $this->responseCode;
	}

	/**
	 * Change le contenu de la page a envoyer
	 *
	 * @param string $responseText le nouveau contenu
	 */
	public function setResponseText($responseText) {
		$this->responseText = $responseText;
	}

	/**
	 * @return string le contenu actuel de la page
	 */
	public function getResponseText() {
		return $this->responseText;
	}

	/**
	 * Ajoute un header à la liste à envoyer lors de l'envoi de la page
	 *
	 * @param string $header un header HTTP valide
	 */
	public function addHeader($header) {
		$this->headers[] = $header;
	}

	/**
	 * Efface les headers de la réponse
	 */
	public function clearHeaders() {
		$this->headers = [];
	}

	/**
	 * Envoie le contenu final de la page et les headers.
	 */
	public function send() {
		$this->sendHeaders();

		echo $this->responseText;
	}

	/**
	 * Envoie les headers HTTP
	 */
	private function sendHeaders() {
		http_response_code($this->responseCode);

		foreach ($this->headers as $header) {
			header($header, true, $this->responseCode);
		}
	}
} 