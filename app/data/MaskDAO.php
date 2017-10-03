<?php

namespace App\Data\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="mask",    options={"collate"="utf8_czech_ci"})
 */
class MaskDAO extends \Kdyby\Doctrine\Entities\BaseEntity {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="date", type="datetime")
     */
    protected $dateUpload;

    /**
     * @ORM\Column(type="integer")
     */
    protected $universal;

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
     * @ORM\Column(name="user_id", type="integer")
     */
    protected $author;

    /**
     * @ORM\Column(name="note", type="string")
     */
    protected $note;

    /**
     * @ORM\Column(name="name", type="string")
     */
    protected $maskName;

    /**
     * @ORM\Column(name="fileSize", type="integer")
     */
    protected $fileSize;

    /**
     * @ORM\Column(name="format", type="string")
     */
    protected $format;

    /**
     * @ORM\Column(name="height", type="integer")
     */
    protected $height;

    /**
     * @ORM\Column(name="width", type="integer")
     */
    protected $width;

    function __construct($maskname, $height, $width, $format, $author, $preview, $path, $universal = 0, $public = 1, $note = NULL, $size = NULL) {
        $this->author = $author;
        $this->universal = $universal;
        $this->public = $public;
        $this->preview = $preview;
        $this->path = $path;
        $this->note = $note;
        $this->maskName = $maskname;
        $this->fileSize = $size;
        $this->height = $height;
        $this->width = $width;
        $this->format = $format;
    }

    public function getId() {
        return $this->id;
    }

    public function getAuthorId() {
        return $this->author;
    }

    public function getPreviewId() {
        return $this->preview;
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

    public function isUniversal() {
        if ($this->universal == 1) {
            return true;
        }
        return false;
    }

    public function getWidth() {
        return $this->width;
    }

    public function getHeight() {
        return $this->height;
    }

    public function getNote() {
        return $this->note;
    }

    public function setNote($note) {
        $this->note = $note;
    }

    public function getName() {
        return $this->maskName;
    }

    public function setName($name) {
        $this->maskName = $name;
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

    public function makeUniversal() {
        $this->universal = 1;
    }

    public function makeCustom() {
        $this->universal = 0;
    }

}
