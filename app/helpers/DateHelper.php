<?php
namespace app\helpers;

use app\Kernel;
use Datetime;
use Exception;

/**
 * Fournit plusieurs méthodes utiles pour la gestion des dates
 *
 * @package app\helpers
 */
class DateHelper {
	/**
	 * Convertis un DateTime au format SQL en une instance de DateTime PHP
	 *
	 * @param string $datestr La date au format SQL
	 * @return DateTime
	 */
	public static function fromSQL($datestr) {
		return DateTime::createFromFormat('Y-m-d G:i:s', $datestr);
	}

	/**
	 * Convertis un DateTime en string contenant la date au format français
	 *
	 * @param Datetime $date
	 * @throws \Exception
	 * @return string
	 */
	public static function frenchFormat(DateTime $date) {
		$locale = setlocale(LC_TIME, '0');

		$newLocale = Kernel::getKernel()->getLocale('fr');
		if (setlocale(LC_TIME, $newLocale) === false)
			throw new Exception('Can\'t change system locale: either it is unimplemented or the locale ('.$newLocale.') does not exist. Configure this in the application Kernel.', 500);

		$date = 'le ' . strftime('%d %B %Y à %Hh%M', $date->getTimestamp());

		setlocale(LC_TIME, $locale);

		return $date;
	}
}