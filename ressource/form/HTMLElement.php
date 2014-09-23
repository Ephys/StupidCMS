<?php
namespace app\ressource\form;

/**
 * Classe servant de base aux éléments HTML stockés en PHP
 *
 * @package app\ressource\form
 */
abstract class HTMLElement {
	/** @var string[] les attributs html de l'élément */
	protected $attrs = [];

	/** @var string le type de balise */
	private $type;

	/** @var boolean la balise est orpheline ou non */
	private $isVoid;

	/**
	 * Change le type d'élément HTML
	 *
	 * @param string $type le type d'élément
	 */
	protected function setType($type) {
		$this->type = $type;
	}

	/**
	 * Récupère le type d'élément HTML
	 *
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Récupère les attributs de l'élément HTML
	 *
	 * @return string[]
	 */
	public function getAttrs() {
		return $this->attrs;
	}

	/**
	 * Définis les attributs de l'élément
	 *
	 * @param string[] $attrs
	 */
	protected function setAttrs(array $attrs) {
		$this->attrs = $attrs;
	}

	/**
	 * Remplace la valeur d'attribut de l'élément HTML par celui désiré
	 * Les clés doivent être entièrement lowercase !
	 *
	 * @param string $key   Le nom de l'attribut, lowercase
	 * @param mixed  $value La valeur de l'attribut
	 * @return $this
	 * @throws \InvalidArgumentException type mismatch
	 */
	public function setAttr($key, $value) {
		$this->attrs[$key] = $value;

		return $this;
	}

	/**
	 * Renvoie la valeur de l'attribut demandé
	 *
	 * @param string $key Le nom de l'attribut
	 * @return null|string      La valeur de l'attribut
	 */
	public function getAttr($key) {
		return (isset($this->attrs[$key]) ? $this->attrs[$key] : null);
	}

	/**
	 * Retire un attribut de l'élément HTML
	 * Les clés doivent être entièrement lowercase !
	 *
	 * @param string $key Le nom de l'attribut, lowercase
	 */
	public function removeAttr($key) {
		unset($this->attrs[$key]);
	}

	/**
	 * Définis si l'élément est un Void Element (ne requière qu'une balise ouvrante)
	 *
	 * @return bool
	 */
	public function isVoid() {
		return $this->isVoid;
	}

	/**
	 * Définis si l'élément est un Void Element (ne requière qu'une balise ouvrante)
	 *
	 * @param bool $void L'élément est void
	 * @return $this
	 */
	protected function setVoid($void) {
		$this->isVoid = (bool)$void;

		return $this;
	}
}