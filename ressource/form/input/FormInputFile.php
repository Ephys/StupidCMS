<?php
namespace app\ressource\form\input;

use app\ressource\FileUploadHandler;
use app\ressource\form\FormHandler;
use Exception;

class FormInputFile extends FormEntry {
	/** @var string[] Mimetypes autorisés */
	private $allowedMimetypes;

	/** @var FileUploadHandler gestionnaire du fichier */
	private $file;

	/** @var int taille maximale du fichier */
	private $maxSize = 0;

	const MIME_IMAGE = 'image/*';
	const MIME_VIDEO = 'video/*';
	const MIME_AUDIO = 'audio/*';

	const ERR_MIMETYPE = 2;
	const ERR_WRONG_SIZE = 3;
	const ERR_FILEUPLOAD = 4;

	public function __construct($name, array $attrs = []) {
		parent::__construct($name, 'file', $attrs);
	}

	public function setForm(FormHandler $form) {
		$form->setEnctype(FormHandler::ENCTYPE_DATA);

		if ($form->getMethod() === 'GET')
			throw new Exception('FormInputFile: A file input parent form cannot have a GET submit method.', 500);

		return parent::setForm($form);
	}

	public function setContent($content) {
		$this->file = new FileUploadHandler($content['name'], $content['type'], $content['location'], $content['error'], $content['size']);

		$this->value = '';
	}

	protected function validate() {
		if ($this->maxSize > 0 && $this->file->getSize() > $this->maxSize) {
			$this->errorNum = self::ERR_WRONG_SIZE;

			return;
		}

		if (!empty($this->allowedMimetypes) && !in_array($this->file->getMimeType(), $this->allowedMimetypes)) {
			$this->errorNum = self::ERR_MIMETYPE;

			return;
		}

		if (!$this->file->isValid())
			$this->errorNum = self::ERR_FILEUPLOAD;

		parent::validate();
	}

	/**
	 * Renvoie le gestionnaire de fichiers
	 *
	 * @return FileUploadHandler
	 */
	public function getFile() {
		return $this->file;
	}

	/**
	 * Définis les mimetypes autorisés.
	 *
	 * @param array $mimeTypes
	 * @return $this
	 */
	public function setMimeTypes(array $mimeTypes) {
		$this->allowedMimetypes = $mimeTypes;

		$this->setAttr('accept', join(', ', $mimeTypes));

		return $this;
	}

	/**
	 * Définis la taille en Byte maximale pour le fichier
	 *
	 * @param int $maxSize
	 * @return $this
	 */
	public function setMaxSize($maxSize) {
		$this->maxSize = $maxSize;

		return $this;
	}
}