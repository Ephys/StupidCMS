<?php
namespace app\ressource\form\input;

use app\ressource\form\FormHandler;
use app\ressource\form\HTMLElement;

/**
 * Element HTML "input" de base, utilisé par le FormHandler et le FormDrawer
 *
 * @package app\ressource\form\input
 */
class FormEntry extends HTMLElement {
	/** @var string $name */
	private $name;

	/** @var mixed $value */
	protected $value;

	/** @var boolean $isRequired */
	protected $isRequired;

	/** @var callable $validator */
	private $validator = null;

	/** @var string */
	private $displayName;

	/** @var FormHandler */
	private $parentForm;

	/** @var string numéro d'erreur, -1 si aucune erreur n'est survenue */
	protected $errorNum = -1;

	const ERROR_FIELD_REQUIRE_EMPTY = 0;
	const ERROR_VALIDATOR           = 1;

	public function __construct($name, $fieldType = 'text', array $attrs = []) {
		if (!is_string($name))
			throw new \InvalidArgumentException('FormEntry::construct: name must be a string', 500);

		$this->setType('input');
		$this->setAttrs($attrs);
		$this->setAttr('type', $fieldType);
		$this->setVoid(true);
		$this->setRequired(true);

		$this->name        = $name;
		$this->displayName = ucwords($name);
	}

	public function getFieldType() {
		return $this->getAttr('type');
	}

	/**
	 * Sets the input parent form
	 *
	 * @param FormHandler $form
	 * @return $this
	 */
	public function setForm(FormHandler $form) {
		$this->parentForm = $form;

		return $this;
	}

	/**
	 * @return FormHandler the input parent form
	 */
	public function getForm() {
		return $this->parentForm;
	}

	/**
	 * Change le nom affiché de l'input (label)
	 * Vaut par défaut le nom de l'input (FormEntry::getName)
	 *
	 * @param string $name Le nouveau nom de l'input
	 * @return $this
	 */
	public function setDisplayName($name) {
		$this->displayName = $name;

		return $this;
	}

	/**
	 * @return string le nom affiché de l'input
	 * Vaut par défaut le nom de l'input (FormEntry::getName)
	 */
	public function getDisplayName() {
		return $this->displayName;
	}

	/**
	 * @return string the input name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Sets the input data to $content
	 *
	 * @param mixed $content the input data
	 * @return $this
	 */
	public function setContent($content) {
		$this->value = trim($content);

		$this->validate();

		if ($this->isValid())
			$this->setAttr('value', $this->value);

		return $this;
	}

	/**
	 * @return mixed la valeur de l'input
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * Sets the input validator to check if the received data is valid or not
	 *
	 * @param callable|null $validator
	 * @throws \InvalidArgumentException type mismatch
	 * @return $this
	 */
	public function setValidator(callable $validator) {
		$this->validator = $validator;

		return $this;
	}

	protected function validate() {
		if ($this->value === null && $this->isRequired) {
			$this->errorNum = self::ERROR_FIELD_REQUIRE_EMPTY;

			return;
		}

		$validator = $this->validator;

		if ($validator !== null && !$validator($this->value)) {
			$this->errorNum = self::ERROR_VALIDATOR;
		}
	}

	/**
	 * @return bool the input is valid
	 */
	public function isValid() {
		return $this->errorNum === -1;
	}

	/**
	 * @return string Le code de l'erreur qui est survenue, -1 si aucune erreur n'est survenue
	 */
	public function getErrorNum() {
		return $this->errorNum;
	}

	/**
	 * @return bool L'input est requis lors de la validation du formulaire
	 */
	public function isRequired() {
		return $this->isRequired;
	}

	/**
	 * Sets the input to required
	 *
	 * @param bool $required
	 * @return $this
	 */
	public function setRequired($required) {
		$this->isRequired = (bool)$required;

		if ($this->isRequired)
			$this->setAttr('required', 'required');
		else
			$this->removeAttr('required');

		return $this;
	}
}