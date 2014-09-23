<?php
namespace app\helpers;

/**
 * Fournit plusieurs méthodes utiles pour la manipulation des arrays
 *
 * @package app\helpers
 */
class ArrayHelper {
	/**
	 * Vérifie si un array est indexé ou associatif
	 *
	 * @param array $arr
	 * @return bool
	 */
	public static function isIndexed(array $arr) {
		$arrLen = count($arr);

		return $arrLen === 0 || array_keys($arr) === range(0, $arrLen - 1);
	}

	/**
	 * Vérifie si un array est associatif ou indexé
	 *
	 * @param array $arr
	 * @return bool
	 */
	public static function isAssoc($arr) {
		return !self::isIndexed($arr);
	}
}