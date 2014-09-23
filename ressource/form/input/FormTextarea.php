<?php
namespace app\ressource\form\input;

/**
 * Element HTML "textarea", utilisé par le FormHandler et le FormDrawer
 *
 * @package app\ressource\form\input
 */
class FormTextarea extends FormInputText {
	public function __construct($name, array $attrs = []) {
		parent::__construct($name, $attrs);

		$this->setType('textarea');
		$this->setVoid(false);
	}

	public function getFieldType() {
		return 'textarea';
	}

	public function setContent($content) {
		$this->value = $content;

		$this->validate();

		return $this;
	}
}