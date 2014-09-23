<?php
namespace app;

/**
 * Gestionnaire d'erreurs: convertis les erreurs en exceptions si possible.
 *
 * @package app
 */
class ErrorManager {
	/** @var string */
	private static $formerErrorHandler;

	/**
	 * Enregistre un error handler convertissant les erreurs en une exception correspondante
	 */
	public static function register() {
		$errTypes = [E_ERROR => 'Error', E_WARNING => 'Warning', E_NOTICE => 'Notice', E_USER_ERROR => 'User Error', E_USER_WARNING => 'User Warning', E_USER_NOTICE => 'User Notice', E_STRICT => 'Runtime Notice', E_RECOVERABLE_ERROR => 'Catchable Fatal Error'];

		self::$formerErrorHandler = set_error_handler((function ($errno, $errmsg, $filename, $linenum, array $vars) use ($errTypes) {
			switch ($errno) {
				case E_ERROR:
				case E_RECOVERABLE_ERROR:
				case E_USER_ERROR:
					throw new \ErrorException('[' . $errTypes[$errno] . '] ' . $errmsg, 500);

				case E_WARNING:
				case E_USER_WARNING:
				case E_NOTICE:
				case E_USER_NOTICE:
					throw new \Exception('[' . $errTypes[$errno] . '] ' . $errmsg, 500);

				case E_DEPRECATED:
				case E_USER_DEPRECATED:
				case E_STRICT:
					if (Kernel::getKernel()->isDevMode()) {
						throw new \Exception($errmsg, $errno);
					}
			}
		}), E_ALL + E_STRICT);
	}

	/**
	 * Simple alias de restore_error_handler().
	 *
	 * @link http://www.php.net/manual/en/function.restore-error-handler.php
	 * @return bool
	 */
	public static function unregister() {
		return restore_error_handler();
	}
} 