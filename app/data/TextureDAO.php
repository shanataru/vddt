<?php

namespace App\Data\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="texture",    options={"collate"="utf8_czech_ci"})
 */
class TextureDAO extends \Kdyby\Doctrine\Entities\BaseEntity {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="date_upload", type="datetime")
     */
    protected $dateUpload;

    /**
     * @ORM\Column(type="string")
     */
    protected $format;

    /**
     * @ORM\Column(type="integer")
     */
    protected $size;

    /**
     * @ORM\Column(name="date", type="datetime")
     */
    protected $takenAtDate;

    /**
     * @ORM\Column(name="location", type="string")
     */
    protected $takenAtLocation;

    /**
     * @ORM\Column(name="user_id", type="integer")
     */
    protected $author;

    /**
     * @ORM\Column(name="take_id", type="integer")
     */
    protected $take;

    /**
     * @ORM\Column(name="move_id", type="integer")

     */
    protected $move;

    /**
     * @ORM\Column(name="preview_id", type="integer")
     */
    protected $preview;

    /**
     * @ORM\Column(name="path", type="string")

     */
    protected $path;

    /**
     * @ORM\Column(name="public", type="integer")
     */
    protected $public;

    /**
     * @ORM\Column(name="processing_id", type="integer")
     */
    protected $processing;

    /**
     * @ORM\Column(name="note", type="string")
     */
    protected $note;

    /**
     * @ORM\Column(name="name", type="string")
     */
    protected $texturename;

    /**
     * @ORM\Column(name="height", type="integer")
     */
    protected $height;

    /**
     * @ORM\Column(name="width", type="integer")
     */
    protected $width;

    function __construct($name, $height, $width, $format, $size, $author, $preview, $path, $take = NULL, $move = NULL, $takenAtDate = NULL, $takenAtLocation = NULL, $public = 1, $processing = NULL, $note = NULL) {
	$this->note = $note;
	$this->height = $height;
	$this->width = $width;
	$this->format = $format;
	$this->author = $author;
	$this->take = $take;
	$this->takenAtDate = $takenAtDate;
	$this->takenAtLocation = $takenAtLocation;
	$this->move = $move;
	$this->public = $public;
	$this->processing = $processing;
	$this->preview = $preview;
	$this->path = $path;
	$this->size = $size;
	$this->texturename = $name;
    }

    public function getId() {
	return $this->id;
    }

    public function getNote() {
	return $this->note;
    }

    public function getName() {
	return $this->texturename;
    }
    
    public function setName($name) {
	$this->texturename = $name;
    }

    public function getFormat() {
	return $this->format;
    }

    public function getSize() {
	return $this->size;
    }

    public function getAuthorId() {
	return $this->author;
    }

    public function getCameraTakeId() {
	return $this->take;
    }
    
    public function setCameraTakeId($id) {
	$this->take = $id;
    }

    public function getCameraMoveId() {
	return $this->move;
    }
    
     public function setCameraMoveId($id) {
	$this->move = $id;
    }

    public function getProcessingId() {
	return $this->processing;
    }

    public function getPreviewId() {
	return $this->preview;
    }

    public function getPath() {
	return $this->path;
    }

    public function getWidth() {
	return $this->width;
    }

    public function getHeight() {
	return $this->height;
    }
    

    public function isPublic() {
	if ($this->public == 1) {
	    return true;
	}
	return false;
    }

    public function getLocation() {
	return $this->takenAtLocation;
    }

    public function getDate() {
	return $this->takenAtDate;
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
	$this->public = 0;
    }

    public function makePublic() {
	$this->public = 1;
    }

}
