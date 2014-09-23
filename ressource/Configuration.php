<?php
namespace app\ressource;

use Exception;

/**
 * Class Configuration
 * Gère un fichier de configuration json
 *
 * @package app\ressource
 */
class Configuration {
	/** @var string */
	private $filename;

	/** @var array|null */
	private $data;

	/** @var */
	private $edited = false;

	public function __construct($filename) {
		$this->filename = $filename;
	}

	public function __destruct() {
		if ($this->edited)
			$this->saveConfig();
	}

	/**
	 * Récupère la valeur de la clé dans la configuration.
	 * Si elle n'existe pas, renvoie la valeur par défaut
	 *
	 * @param string $key     Clé recherchée
	 * @param mixed  $default Valeur par défaut à renvoyer
	 * @return mixed
	 */
	public function get($key, $default = null) {
		if ($this->data === null)
			$this->loadConfig();

		if (!isset($this->data[$key])) {
			$this->set($key, $default);
		}

		return $this->data[$key];
	}

	/**
	 * Modifie la valeur d'une entrée de la configuration
	 *
	 * @param string $key   Nom de l'entrée
	 * @param mixed  $value Valeur de l'entrée
	 * @return $this
	 */
	public function set($key, $value) {
		$this->data[$key] = $value;

		$this->edited = true;

		return $this;
	}

	/**
	 * Charge le fichier de configuration en mémoire
	 *
	 * @throws Exception
	 */
	private function loadConfig() {
		if (!file_exists($this->filename)) {
			$this->data = [];
		} else {
			$rawData = file_get_contents($this->filename);

			$parsedData = json_decode($rawData, true);

			if (!is_array($parsedData)) {
				copy($this->filename, $this->filename . '.bak');

				$this->data = [];
			} else {
				$this->data = $parsedData;
			}
		}
	}

	/**
	 * Sauvegarde le fichier de configuration sur le disque
	 */
	private function saveConfig() {
		if (file_exists($this->filename))
			copy($this->filename, $this->filename . '.bak');

		file_put_contents($this->filename, json_encode($this->data));

		$this->edited = false;
	}
}