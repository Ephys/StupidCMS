<?php
namespace app\ressource\form\input;

/**
 * Element HTML "input" de type email, utilisé par le FormHandler et le FormDrawer
 *
 * @package app\ressource\form\input
 */
class FormInputEmail extends FormInputText {
	const ERROR_FIELD_NOT_EMAIL = 2;

	protected function validate() {
		if ($this->value !== null && !filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
			$this->errorNum = self::ERROR_FIELD_NOT_EMAIL;

			return;
		}

		parent::validate();
	}

	public function __construct($name, array $attrs = []) {
		parent::__construct($name, $attrs);

		$this->setAttr('type', 'email');
	}
} 