<?php
namespace app\ressource;

use PDO;

/**
 * Gestionnaire de bases de données MySQL
 *
 * @package app\ressource
 */
class Database {
	/** @var string[][] les données de connexion */
	private $keys = [];

	/** @var PDO[] les instances de connexion */
	private $instances = [];

	/**
	 * Créé une instance de PDO MySQL
	 *
	 * @param $name
	 * @return PDO
	 */
	private function getPDO($name) {
		$bdd = new PDO('mysql:host=' . $this->keys[$name]['host'] . ';dbname=' . $this->keys[$name]['db'], $this->keys[$name]['user'], $this->keys[$name]['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
		$bdd->exec("SET NAMES 'utf8'");

		return $bdd;
	}

	/**
	 * Enregistre les données nécéssaires à instance pour une connexion MySQL
	 *
	 * @param        $data
	 * @param string $name
	 */
	public function addInstance($data, $name = 'default') {
		$this->keys[$name] = $data;
	}

	/**
	 * Retourne l'instance de PDO associée à $name, la créé si elle existe
	 *
	 * @param string $name
	 * @return PDO
	 */
	public function getInstance($name = 'default') {
		if (!isset($this->instances[$name])) {
			return $this->instances[$name] = $this->getPDO($name);
		}

		return $this->instances[$name];
	}
}