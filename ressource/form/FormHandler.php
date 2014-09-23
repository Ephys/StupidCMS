<?php
namespace app\ressource\form;

use app\ressource\form\input\FormEntry;
use IteratorAggregate;

/**
 * Gestionnaire de formulaires
 * Permet de créer et valider automatiquement des formulaires
 * Utilisez app\ressource\form\FormDrawer pour afficher un FormHandler
 *
 * @package app\ressource\form
 */
class FormHandler extends HTMLElement implements IteratorAggregate {
	/** @var FormEntry[] les inputs du formulaire */
	private $inputs = [];

	/** @var string Le nom du formualire */
	private $name;

	/** @var int La méthode d'envoi du formulaire (Get, post, etc) */
	private $method;

	/** @var string l'url vers laquelle le formulaire doit envoyer les données */
	private $action;

	/** @var mixed[] les données non traitées du formulaire */
	private $formData;

	/** @var string Enctype du formulaire */
	private $enctype;

	const TYPE_POST = 0;
	const TYPE_GET  = 1;

	const ENCTYPE_URLENCODE = 'application/x-www-form-urlencoded';
	const ENCTYPE_TEXT = 'text/plain';
	const ENCTYPE_DATA = 'multipart/form-data';

	private static $methods = ['POST', 'GET'];
	private static $errorMsg = ['The form hasn\'t been found in the request data', 'A required field hasn\'t been found in the request data. Use FormHandler::getErroredField()', 'A field is invalid. Use FormHandler::getErroredField()'];

	/** @var string numéro d'erreur, -1 si aucune erreur n'est survenue */
	private $errorNum = -1;

	/** @var FormEntry L'input posant problème dans le traitement (erreurs 1 et 2) */
	private $erroredField = null;

	const ERROR_FINE            = -1;
	const ERROR_FORM_NOT_FOUND  = 0;
	const ERROR_FIELD_NOT_FOUND = 1;
	const ERROR_FIELD_INVALID   = 2;

	/**
	 * Créé un nouveau formulaire
	 *
	 * @param string $name   Le nom du formulaire
	 * @param string $action L'url de redirection du formulaire
	 * @param int    $type   Le type de requête demandé par le formulaire: FormHandler::TYPE_POST ou FormHandler::TYPE_GET
	 * @param array  $attrs  Les attributs du formulaire HTML
	 * @throws \InvalidArgumentException
	 */
	public function __construct($name, $action, $type = self::TYPE_POST, array $attrs = []) {
		if (!is_string($name))
			throw new \InvalidArgumentException('FormHandler::construct: name must be a string', 500);
		if (!is_string($action))
			throw new \InvalidArgumentException('FormHandler::construct: action must be a string', 500);
		if (!is_int($type) || $type < 0 || $type > 1)
			throw new \InvalidArgumentException('FormHandler::construct: type must be either FormHandler::TYPE_POST or FormHandler::TYPE_GET. Invalid type ' . $type, 500);

		$this->setAttrs($attrs);
		$this->setType('form');

		$this->name   = $name;
		$this->method = $type;
		$this->action = $action;
	}

	/**
	 * @return string the form action
	 */
	public function getAction() {
		return $this->action;
	}

	/**
	 * @return string the form method
	 */
	public function getMethod() {
		return self::$methods[$this->method];
	}

	/**
	 * Prévu pour l'autogestion des erreurs
	 *
	 * @return int Le code d'erreur, -1 si aucune erreur n'est survenue
	 */
	public function getError() {
		return $this->errorNum;
	}

	/**
	 * Prévu pour le débug des erreurs, référez-vous aux constantes et à getError() pour les traiter automatiquement
	 *
	 * @return string Le message d'erreur, null si aucune erreur n'est survenue
	 */
	public function getErrorMessage() {
		if ($this->errorNum === -1)
			return null;

		return self::$errorMsg[$this->errorNum];
	}

	/**
	 * @return FormEntry L'input posant problème lors du traitement
	 */
	public function getErroredField() {
		return $this->erroredField;
	}

	/**
	 * Récupère les données envoyées et vérifie qu'elles respectent les demandes
	 *
	 * @return bool Les données sont valides
	 */
	public function parse() {
		switch ($this->method) {
			case self::TYPE_GET:
				$method = $_GET;
				break;
			case self::TYPE_POST:
				$method = $_POST;
				break;
		}

		if (!isset($method[$this->name])) {
			return $this->setInvalid(self::ERROR_FORM_NOT_FOUND);
		}

		$formData = $method[$this->name];

		if ($this->enctype === self::ENCTYPE_DATA) {
			if (!isset($_FILES[$this->name])) {
				return $this->setInvalid(self::ERROR_FORM_NOT_FOUND);
			}

			foreach ($_FILES[$this->name]['name'] as $key => $value) {
				$formData[$key] = [
					'name' => $value,
					'type' => $_FILES[$this->name]['type'][$key],
					'location' => $_FILES[$this->name]['tmp_name'][$key],
					'error' => $_FILES[$this->name]['error'][$key],
					'size' => $_FILES[$this->name]['size'][$key]
				];
			}
		}

		$this->formData = $formData;

		foreach ($this->inputs as $input) {
			if (!isset($formData[$input->getName()]) || (is_string($formData[$input->getName()]) && trim($formData[$input->getName()]) === '')) {
				if ($input->isRequired())
					return $this->setInvalid(self::ERROR_FIELD_NOT_FOUND, $input);
				else
					$input->setContent(null);
			}
			else {
				$input->setContent($formData[$input->getName()]);

				if (!$input->isValid())
					return $this->setInvalid(self::ERROR_FIELD_INVALID, $input);
			}
		}

		return true;
	}

	private function setInvalid($error, $field = null) {
		$this->errorNum     = $error;
		$this->erroredField = $field;

		return false;
	}

	/**
	 * @return bool Le formulaire et ses inputs sont valides
	 */
	public function isValid() {
		if (!$this->errorNum !== -1)
			return false;

		foreach ($this->inputs as $input) {
			if (!$input->isValid())
				return false;
		}

		return true;
	}

	/**
	 * Attention: appeler FormHandler::parse() pour récupérer les données avant.
	 *
	 * @return \mixed[] Les données du formulaire
	 */
	public function getFormData() {
		return $this->formData;
	}

	/**
	 * Renvoie le contenu d'un input du formulaire
	 *
	 * @param string $name Le nom de l'input
	 * @return mixed    Le contenu de l'input
	 */
	public function getInputData($name) {
		return $this->inputs[$name]->getValue();
	}

	/**
	 * @return string the form name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param FormEntry $input the FormEntry to add to the Form
	 * @return $this
	 * @throws \InvalidArgumentException type mismatch
	 */
	public function addInput(FormEntry $input) {
		if (!empty($this->inputs[$input->getName()]))
			throw new \InvalidArgumentException('FormHandler::addInput: input name "' . $input->getName() . '" already in use in this form', 500);

		$this->inputs[$input->getName()] = $input;
		$input->setForm($this);

		return $this;
	}

	/**
	 * Get named FormEntry instance
	 *
	 * @param string $inputName the input name
	 * @return FormEntry the required input
	 */
	public function getInput($inputName) {
		return $this->inputs[$inputName];
	}

	/**
	 * Vérifie si l'élément demandé existe dans le formulaire
	 * Attention: appeler FormHandler::parse() pour récupérer les données avant.
	 *
	 * @param string $key Le nom de l'élément recherché
	 * @return bool         L'élément existe
	 */
	public function has($key) {
		return isset($this->formData[$key]);
	}

	/**
	 * @return bool le formulaire est présent dans la requête
	 */
	public function exist() {
		switch ($this->method) {
			case self::TYPE_GET:
				$method = $_GET;
				break;
			case self::TYPE_POST:
				$method = $_POST;
				break;
		}

		return isset($method[$this->name]);
	}

	public function getIterator() {
		return new \ArrayIterator($this->inputs);
	}

	/**
	 * Resets the form inputs data
	 */
	public function clear() {
		foreach ($this->inputs as $input) {
			$input->setContent('');
		}
	}

	/**
	 * Change l'enctype du formulaire
	 *
	 * @param $enctype
	 */
	public function setEnctype($enctype) {
		$this->setAttr('enctype', $enctype);

		$this->enctype = $enctype;
	}
}