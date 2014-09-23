<?php
namespace app\ressource\form;

use app\ressource\form\input\FormEntry;

/**
 * Class FormDrawer
 *
 * @package app\ressource\form
 *
 * Cette classe à pour but de convertir un formulaire de la classe FormHandler en du code HTML5
 * utilisable par une vue.
 *
 * Elle est conçue de manière extrèmement modulaire afin de faciliter sa personnalisation via
 * l'implémentation d'une extension de FormDrawer.
 *
 * Projet scolaire: Une extension ne sera pas implémentée pour le projet, cette version est déjà
 * écrite pour répondre à nos besoins.
 */
class FormDrawer {
	/**
	 * Convertit un formulaire au format HTML5
	 *
	 * @param FormHandler $form Le formulaire à afficher
	 * @param array       $data données additionnelles pouvant être utilisées par une extenion de FormDrawer
	 * @return string Le formulaire au format HTML
	 */
	public static function drawForm(FormHandler $form, $data = []) {
		$html = '<form' . self::drawAttributes($form) . ' action="' . $form->getAction() . '" method="' . $form->getMethod() . '">';
		$html .= '<fieldset>';
		if (isset($data['legend']))
			$html .= '<legend>' . $data['legend'] . '</legend>';

		$html .= self::drawInputs($form);

		$html .= '</fieldset>';
		$html .= '</form>';

		return $html;
	}

	/**
	 * Convertis les inputs d'un formulaire au format HTML5
	 *
	 * @param FormHandler $form Le formulaire contenant les inputs
	 * @param array       $data Des données additionnelles pouvant être utilisées par une extenion de FormDrawer
	 * @return string
	 */
	public static function drawInputs(FormHandler $form, $data = null) {
		$html = '';

		foreach ($form as $input) {
			$html .= self::drawLabel($input);
			$html .= self::drawInput($input);
		}

		return $html;
	}

	/**
	 * Convertis un input au format HTML5
	 *
	 * @param FormEntry $input L'input à écrire
	 * @param array     $data  Des données additionnelles pouvant être utilisées par une extenion de FormDrawer
	 * @throws \ErrorException
	 * @return string
	 */
	public static function drawInput(FormEntry $input, $data = null) {
		if ($input->isVoid())
			return '<' . $input->getType() . self::drawAttributes($input) . 'name="' . self::getInputName($input) . '">';

		switch ($input->getFieldType()) {
			case 'textarea':
				return self::drawNonVoidInput($input);

			default:
				throw new \ErrorException('Unimplemented input ' . $input->getFieldType() . ' drawer', 500);
		}
	}

	/**
	 * Convertis un input au format HTML5
	 *
	 * @param FormEntry $input L'input à écrire
	 * @param array     $data  Des données additionnelles pouvant être utilisées par une extenion de FormDrawer
	 * @return string
	 */
	public static function drawNonVoidInput(FormEntry $input, $data = null) {
		return '<' . $input->getType() . self::drawAttributes($input) . ' name="' . self::getInputName($input) . '">' . htmlspecialchars($input->getValue()) . '</' . $input->getType() . '>';
	}

	/**
	 * Extrait le label d'un FormEntry et l'écrit au format HTML5
	 *
	 * @param FormEntry $input
	 * @param array     $data données additionnelles pouvant être utilisées par une extenion de FormDrawer
	 * @return string
	 */
	public static function drawLabel(FormEntry $input, $data = null) {
		if ($input->getDisplayName() === '')
			return '';

		$fieldType = $input->getFieldType();
		if ($fieldType === 'submit' || $fieldType === 'reset' || $fieldType === 'button')
			return '';

		return '<label for="' . self::getInputID($input) . '">' . $input->getDisplayName() . '</label>';
	}

	/**
	 * Returns a form input (supposed unique) ID
	 *
	 * @param FormEntry $input the input to extract the ID for
	 * @return string the input id
	 */
	public static function getInputID(FormEntry $input) {
		return $input->getForm()->getName() . '_' . $input->getName();
	}

	/**
	 * Returns a form input (supposed unique) Name
	 *
	 * @param FormEntry $input the input to extract the name for
	 * @return string the input name
	 */
	public static function getInputName(FormEntry $input) {
		return $input->getForm()->getName() . '[' . $input->getName() . ']';
	}

	/**
	 * Convertis les attributs d'un élément HTML au format HTML5
	 *
	 * @param HTMLElement $element
	 * @return string
	 */
	public static function drawAttributes(HTMLElement $element) {
		$attrs = $element->getAttrs();

		$returns = ' ';

		foreach ($attrs as $key => $value) {
			$returns .= $key . '="' . htmlspecialchars(addcslashes($value, '"\\')) . '" ';
		}

		return $returns;
	}
}