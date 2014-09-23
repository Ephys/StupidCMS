<?php
namespace app\helpers;

/**
 * Fournit plusieurs méthodes utiles pour la manipulation de stings
 *
 * @package app\helpers
 */
class StringHelper {
	/**
	 * Retire le slash à la fin d'un string s'il est présent
	 *
	 * @param  string $input
	 * @return string
	 */
	public static function removeEndSlash($input) {
		if (strlen($input) !== 0 && $input[strlen($input) - 1] === '/')
			return substr_replace($input, '', -1);

		return $input;
	}

	/**
	 * Retire le slash au début d'un string s'il est présent
	 *
	 * @param  string $input
	 * @return string
	 */
	public static function removeStartSlash($input) {
		if (isset($input[0]) && $input[0] === '/')
			return substr_replace($input, '', 0, 1);

		return $input;
	}

	/**
	 * Retire tous les caractères non alphanumeriques
	 *
	 * @param string $str
	 * @return string
	 */
	public static function makeAlphanumeric($str) {
		return preg_replace('/[^a-zA-Z0-9\s]/', '', $str);
	}

	/**
	 * Réduis la taille d'un string si elle est trop grande
	 *
	 * Les caractères a ajouter en fin de string sont pris en compte dans la taille maximale du string
	 *
	 * @param string $str       Le String à réduire
	 * @param int    $maxLength La taille maximale du string
	 * @param string $marker    Les caractères à ajouter en fin de string si celui-ci est trop grand
	 * @return string
	 */
	public static function chop($str, $maxLength, $marker = '') {
		if (strlen($str) > $maxLength)
			return substr($str, 0, $maxLength - strlen($marker)) . $marker;

		return $str;
	}

	/**
	 * Convertis une variable en string
	 * Renvoie un résultat similaire à json_encode avec un support pour les objets
	 *
	 * @param mixed $item
	 * @param int   $identLevel Le niveau d'indentation (utilisé pour les sous-tableaux)
	 * @return string
	 */
	public static function toString($item, $identLevel = 0) {
		switch (gettype($item)) {
			case 'string':
				return '\'' . $item . '\'';

			case 'array':
				return self::arrayToString($item, $identLevel + 1);

			case 'object':
				if (is_callable($item))
					return '{ anonymous function }';

				if (method_exists($item, '__toString'))
					return '{ ' . $item->__toString() . ' }';
				else
					return get_class($item) . ' ' . self::arrayToString(get_object_vars($item), $identLevel + 1);

			// évite l'affichage sous forme de '1' (ou le non affichage tout court en cas de false)
			case 'boolean':
				return $item ? 'true' : 'false';

			case 'NULL':
				return 'null';

			default:
				return $item;
		}
	}

	/**
	 * Convertis un array en string
	 *
	 * @param array $arr
	 * @param int   $indentLevel Le niveau d'indentation (utilisé pour les sous-tableaux)
	 * @return string
	 */
	public static function arrayToString(array $arr, $indentLevel = 1) {
		$str = '';

		$arrLen              = count($arr);
		$maxElementsForShort = 1;

		$multiline = $arrLen > $maxElementsForShort;
		$isObject  = ArrayHelper::isAssoc($arr);

		if ($isObject)
			$str .= '{';
		else
			$str .= '[';

		if ($multiline)
			$str .= "\n";
		else
			$indentLevel--;

		$i = 0;
		foreach ($arr as $key => $item) {
			if ($multiline)
				$str .= str_repeat("\t", $indentLevel);

			if ($isObject)
				$str .= self::toString($key, $indentLevel) . ': ';

			$str .= self::toString($item, $indentLevel);

			if ($i++ !== $arrLen - 1)
				$str .= ', ';

			if ($multiline)
				$str .= "\n";
		}

		if ($multiline)
			$str .= str_repeat("\t", $indentLevel - 1);

		if ($isObject)
			$str .= '}';
		else
			$str .= ']';

		return $str;
	}

	/**
	 * Retourne sous forme de string ce qu'affiche var_dump()
	 *
	 * @link http://www.php.net/manual/fr/function.var-dump.php
	 *
	 * @param mixed $var Le paramètre de var_dump()
	 * @return string         Ce qu'afficherait var_dump()
	 */
	public static function varDump($var) {
		ob_start();
		var_dump($var);

		return ob_get_clean();
	}
}