<?php
namespace app\ressource\form\input;

/**
 * Element HTML "input" de type number, utilisé par le FormHandler et le FormDrawer
 *
 * @package app\ressource\form\input
 */
class FormInputNumber extends FormEntry {
	/** @var null|int */
	private $min = null;

	/** @var null|int */
	private $max = null;

	const ERROR_FIELD_OUT_OF_BOUNDS = 2;

	public function __construct($name, array $attrs = []) {
		parent::__construct($name, 'number', $attrs);
	}

	protected function validate() {
		if ($this->value !== null && (!is_numeric($this->value) || !$this->inRange())) {
			$this->errorNum = self::ERROR_FIELD_OUT_OF_BOUNDS;

			return;
		}

		parent::validate();
	}

	/**
	 * @return bool la valeur de l'input est compris entre $min et $max
	 */
	private function inRange() {
		return ($this->min === null || $this->value >= $this->min) && ($this->max === null || $this->value <= $this->max);
	}

	public function setContent($content) {
		parent::setContent($content);

		if ($this->isValid() && $this->value !== null)
			$this->value = (int)$content;

		return $this;
	}

	/**
	 * Change la valeur numérique minimum acceptée par l'input. null signifie aucune limite
	 * À préférer à setAttr('min') parce que les données entrées par l'utilisateur
	 * seront également vérifiées
	 *
	 * @param int|null $min
	 * @throws \InvalidArgumentException
	 * @return $this
	 */
	public function setMin($min) {
		if (!is_int($min) && !is_null($min))
			throw new \InvalidArgumentException('FormInputNumber::setMin: min should be of type integer or null');

		$this->min = $min;

		if ($min === null)
			$this->removeAttr('min');
		else
			$this->setAttr('min', $min);

		return $this;
	}

	/**
	 * Change la valeur numérique maximum acceptée par l'input. null signifie aucune limite
	 * À préférer à setAttr('min') parce que les données entrées par l'utilisateur
	 * seront également vérifiées
	 *
	 * @param int|null $max
	 * @throws \InvalidArgumentException
	 * @return $this
	 */
	public function setMax($max) {
		if (!is_int($max) && !is_null($max))
			throw new \InvalidArgumentException('FormInputNumber::setMax: max should be of type integer or null');

		$this->max = $max;

		if ($max === null)
			$this->removeAttr('max');
		else
			$this->setAttr('max', $max);

		return $this;
	}

	/**
	 * @return int|null la valeur minimum autorisée dans l'input
	 */
	public function getMin() {
		return $this->min;
	}

	/**
	 * @return int|null la valeur maximum autorisée dans l'input
	 */
	public function getMax() {
		return $this->max;
	}
}