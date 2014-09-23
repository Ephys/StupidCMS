<?php
namespace app\ressource\form\input;

/**
 * Element HTML "input" de type texte, utilisé par le FormHandler et le FormDrawer
 *
 * @package app\ressource\form\input
 */
class FormInputText extends FormEntry {
	/** @var int */
	protected $maxLength = -1;

	public function __construct($name, array $attrs = []) {
		parent::__construct($name, 'text', $attrs);

		if (isset($attrs['maxlength']))
			$this->maxLength = (int)$attrs['maxlength'];
	}

	protected function validate() {
		$this->isValid = $this->maxLength < 1 || $this->value === null || strlen($this->value) <= $this->maxLength;

		parent::validate();
	}

	/**
	 * Change la taille maximale de l'input
	 * À préférer à setAttr('maxlength') parce que vérifie aussi les données envoyées par l'utilisateur
	 *
	 * @param int $maxLength La taille maximale de l'input, en dessous de 1: pas de limite
	 * @return $this
	 */
	public function setMaxLength($maxLength) {
		$this->setAttr('maxlength', $maxLength);

		$this->maxLength = (int)$maxLength;

		return $this;
	}
}