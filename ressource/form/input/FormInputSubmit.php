<?php
namespace app\ressource\form\input;

/**
 * Element HTML "input" de type submit, utilisé par le FormHandler et le FormDrawer
 *
 * @package app\ressource\form\input
 */
class FormInputSubmit extends FormEntry {
	public function __construct($name, array $attrs = []) {
		parent::__construct($name, 'submit', $attrs);

		$this->setDisplayName(ucfirst($name));

		$this->isRequired = false;
		$this->removeAttr('required');
	}

	public function setDisplayName($name) {
		$this->setAttr('value', $name);

		return parent::setDisplayName($name);
	}

	public function setContent($content) { return $this; }

	public function setRequired($required) { return $this; }
}