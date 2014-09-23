<?php
namespace app\ressource;

/**
 * Classe utilitaire permettant de calculer le temps d'exécution d'une partie de code
 *
 * @package app\ressource
 */
class Profiler {
	/** @var int */
	private $startTime;

	public function __construct() {
		$this->startTime = microtime();
	}

	/**
	 * Renvoie le temps écoulé depuis l'instantiation de la classe
	 *
	 * @return int
	 */
	public function getExecutionTime() {
		return microtime() - $this->startTime;
	}
}