<?php
namespace app\helpers;

use Exception;

/**
 * Fournit plusieurs méthodes utiles pour le débogage
 *
 * @package app\helpers
 */
class DebugHelper {
	/**
	 * Affiche correctement le stack trace, getStackTraceAsString() affichant une version raccourcie
	 *
	 * @param Exception $e
	 * @return string
	 */
	public static function getStackTrace(Exception $e) {
		$traces = $e->getTrace();

		$str = '';

		// hotfix to not display the error manager, which is confusing
		if ($traces[0]['class'] === 'app\\ErrorManager')
			unset($traces[0]);

		foreach ($traces as $num => $trace) {
			$str .= '#' . $num . ' ';

			if (isset($trace['file']))
				$str .= $trace['file'] . '(' . $trace['line'] . '): ';

			if (isset($trace['class']))
				$str .= $trace['class'] . $trace['type'];

			$str .= $trace['function'] . '(';

			$i     = 0;
			$count = count($trace['args']);
			foreach ($trace['args'] as $arg) {
				$str .= StringHelper::toString($arg);

				if ($i++ !== $count - 1)
					$str .= ', ';
			}

			$str .= ')';
			$str .= "\n\n";
		}

		return $str;
	}

	/**
	 * Affiche les données proprement à la manière de StringHelper::toString()
	 *
	 * @param $data
	 */
	public static function cleanDump($data) {
		echo '<pre>' . htmlspecialchars(StringHelper::toString($data)) . '</pre>';
	}

	/**
	 * Affiche les données proprement à la manière de var_dump()
	 *
	 * @link http://www.php.net/manual/fr/function.var-dump.php
	 *
	 * @param $data
	 */
	public static function varDump($data) {
		echo '<pre>' . htmlspecialchars(StringHelper::varDump($data)) . '</pre>';
	}

	/**
	 * Affiche les données proprement à la manière de var_export()
	 *
	 * @link http://www.php.net/manual/fr/function.var-dump.php
	 *
	 * @param $data
	 */
	public static function varExport($data) {
		echo '<pre>' . htmlspecialchars(var_export($data, true)) . '</pre>';
	}
} 