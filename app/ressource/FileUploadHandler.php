<?php
namespace app\ressource;

use app\ressource\exceptions\FileUploadException;

class FileUploadHandler {
	/** @var string nom d'upload du fichier */
	private $name;

	/** @var string  */
	private $type;

	/** @var string chemin temporaire vers le fichier */
	private $location;

	/** @var int code d'erreur de l'upload */
	private $error;

	/** @var int taille du fichier */
	private $size;

	/** @var string[] message d'erreurs pour $error */
	private $errorsMsg = [
		0 => 'Success',
		1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
		2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
		3 => 'The uploaded file was only partially uploaded.',
		4 => 'No file was uploaded.',
		6 => 'Missing a temporary folder.',
		7 => 'Failed to write file to disk. Introduced in PHP 5.1.0.',
		8 => 'A PHP extension stopped the file upload.'
	];

	public function __construct($name, $type, $location, $error, $size) {
		$this->name = $name;
		$this->type = $type;
		$this->location = $location;
		$this->error = $error;
		$this->size = $size;

		if (in_array($this->error, [8, 7, 6]))
			throw new FileUploadException('FileUpload - err #'.$this->error.': '.$this->errorsMsg[$this->error], 500);
	}

	/**
	 * Renvoie le type MIME du fichier (tel que défini par les headers)
	 * Attention, il est possible que ce type MIME soit différent de
	 * celui local.
	 *
	 * @return string
	 */
	public function getClientMimeType() {
		return $this->type;
	}

	/**
	 * Renvoie le type MIME du fichier
	 *
	 * @return string
	 */
	public function getMimeType() {
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_file($finfo, $this->location);
		finfo_close($finfo);

		return $mime;
	}

	/**
	 * Renvoie la taille du fichier
	 *
	 * @return int
	 */
	public function getSize() {
		return $this->size;
	}

	/**
	 * Renvoie le nom du fichier uploadé
	 *
	 * @return int
	 */
	public function getUploadName() {
		return $this->name;
	}

	/**
	 * Déplace le fichier vers le chemin demandé
	 *
	 * @param string $destination
	 * @return bool déplacement réussi
	 */
	public function move($destination) {
		return move_uploaded_file($this->location, $destination);
	}

	/**
	 * @return bool
	 */
	public function isValid() {
		return $this->error === 0;
	}

	/**
	 * Renvoie le code d'erreur PHP correspondant à l'état de l'upload
	 *
	 * @return int
	 */
	public function getErrorNum() {
		return $this->error;
	}

	/**
	 * Renvoie le message d'erreur, pour le debug. Utilisez getErrorNum() pour
	 * automatiser le traitement d'erreurs.
	 *
	 * @return string
	 */
	public function getErrorMessage() {
		return $this->errorsMsg[$this->error];
	}
}