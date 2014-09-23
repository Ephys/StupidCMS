<?php
namespace app\ressource\database;

interface IDBHandler {
	/**
	 * Registers a connection handler
	 *
	 * @param mixed[] $data connection data
	 * @param string $name connection handler name
	 */
	public function addInstance($data, $name = 'default');

	/**
	 * Returns a connection handler
	 *
	 * @param string $name connection handler name
	 */
	public function getInstance($name = 'default');
}