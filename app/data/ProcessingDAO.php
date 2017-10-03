<?php

namespace App\Data\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="processing",    options={"collate"="utf8_czech_ci"})
 */
class ProcessingDAO extends \Kdyby\Doctrine\Entities\BaseEntity {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(name="date_start", type="datetime")
     */
    protected $dateStart;

    /**
     * @ORM\Column(name="status", type="integer")
     */
    protected $status;

    /**
     * @ORM\Column(name="date_end", type="datetime")
     */
    protected $dateEnd;

    /**
     * @ORM\Column(name="binary_id", type="integer")
     */
    protected $binary;

    /**
     * @ORM\Column(name="mask_id", type="integer")
     */
    protected $mask;

    /**
     * @ORM\Column(name="path", type="string")
     */
    protected $path;

    /**
     * @ORM\Column(name="size", type="integer")
     */
    protected $size;

    /**
     * @ORM\Column(name="preview_id", type="integer")
     */
    protected $preview;

    /**
     * @ORM\Column(name="material", type="integer")
     */
    protected $material;

    /**
     * @ORM\Column(name="public", type="integer")
     */
    protected $public;

    /**
     * @ORM\Column(name="rating", type="integer")
     */
    protected $rating;

    /**
     * @ORM\Column(name="user_id", type="integer")
     */
    protected $author;

    function __construct($name, $dateStart, $author, $mask = NULL, $binary = NULL, $path = NULL, $size = NULL, $preview = NULL, $material = NULL, $status = 2, $rating = NULL, $public = 1) {
	$this->name = $name;
	$this->dateStart = $dateStart;
	$this->status = $status;
	$this->binary = $binary;
	$this->mask = $mask;
	$this->path = $path;
	$this->size = $size;
	$this->preview = $preview;
	$this->material = $material;
	$this->rating = $rating;
	$this->public = $public;
	$this->author = $author;
    }

    public function getId() {
	return $this->id;
    }

    public function getState() {
	return $this->status;
    }

    public function setState($status) {
	$this->status = status;
    }

    public function getDateStart() {
	return $this->dateStart;
    }

    public function getDateEnd() {
	return $this->dateEnd;
    }

    public function setDateEnd($dateEnd) {
	$this->dateEnd = $dateEnd;
    }

    public function getMaterialId() {
	return $this->material;
    }

    public function getAuthorId() {
	return $this->author;
    }

    public function getBinaryId() {
	return $this->binary;
    }

    public function getPreviewId() {
	return $this->preview;
    }

    public function getSize() {
	return $this->size;
    }

    public function getRating() {
	return $this->rating;
    }
    
    public function setRating($rating) {
	$this->rating = $rating;
    }

    public function getMaskId() {
	return $this->mask;
    }

    public function getPath() {
	return $this->path;
    }

    public function isPublic() {
	if ($this->public == 1) {
	    return true;
	}
	return false;
    }

    /**
     * @param $newAuthorId
     */
    public function setAuthor($newAuthorId) {
	$this->author = $newAuthorId;
    }

    public function makePrivate() {
	$this->private = 0;
    }

    public function makePublic() {
	$this->private = 1;
    }

}
