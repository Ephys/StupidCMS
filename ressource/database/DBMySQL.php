<?php
namespace app\ressource\database;

use PDO;

/**
 * DBMySQL
 *
 * @package app\ressource\database
 */
class DBMySQL implements IDBHandler {
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
	 * Registers a MySQL connection using PDO
	 *
	 * @param mixed[] $data connection data, must contain keys 'host', 'db', 'user' and 'password'
	 * @param string $name
	 */
	public function addInstance($data, $name = 'default') {
		$this->keys[$name] = $data;
	}

	public function getInstance($name = 'default') {
		if (!isset($this->instances[$name])) {
			return $this->instances[$name] = $this->getPDO($name);
		}

		return $this->instances[$name];
	}
}