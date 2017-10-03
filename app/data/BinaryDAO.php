<?php

namespace App\Data\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="binaryCodes",    options={"collate"="utf8_czech_ci"})
 */
class BinaryDAO extends \Kdyby\Doctrine\Entities\BaseEntity {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    /**
     * @ORM\Column(name="user_id", type="integer")
     */
    protected $author;
    /**
     * @ORM\Column(name="date", type="datetime")
     */
    protected $dateUpload;

    /**
     * @ORM\Column(name="fileSize", type="integer")
     */
    protected $fileSize;

    /**
     * @ORM\Column(name="language", type="string")
     */
    protected $language;

    /**
     * @ORM\Column(name="path", type="string")

     */
    protected $path;

    /**
     * @ORM\Column(name="public", type="integer")
     */
    protected $public;
    
    /**
     * @ORM\Column(name="note", type="string")
     */
    protected $note;

    /**
     * @ORM\Column(name="name", type="string")
     */
    protected $binaryname;


    function __construct($name, $author, $path, $public = 1, $fileSize = NULL, $note = NULL, $language=NULL ) {
		$this->author = $author;
		$this->fileSize = $fileSize;
		$this->public = $public;
		$this->language = $language;
		$this->path = $path;
		$this->note = $note;
		$this->binaryname = $name;
    }

    public function getId() {
	return $this->id;
    }

    public function getAuthorId() {
	return $this->author;
    }

    public function getSize() {
	return $this->fileSize;
    }
    
    public function getLanguage() {
	return $this->language;
    }

    public function setLanguage($language) {
        $this->language = $language;
    }

    public function getPath() {
	return $this->path;
    }
    
    public function getNote() {
	return $this->note;
    }

    public function setNote($note) {
        $this->note = $note;
    }
    
    public function getName() {
	return $this->binaryname;
    }

    public function setName($name) {
        $this->binaryname = $name;
    }

    public function isPublic() {
	if ($this->public == 1) {
	    return true;
	}
	return false;
    }
    

    public function uploadedAt() {
	return $this->dateUpload;
    }

    /**
     * @param $newAuthorId
     */
    public function setAuthor($newAuthorId) {
	$this->author = $newAuthorId;
    }

    public function makePrivate() {
	   $this->public = 1;
    }

    public function makePublic() {
	   $this->public = 0;
    }

}
