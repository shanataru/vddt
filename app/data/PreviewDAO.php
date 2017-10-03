<?php

namespace App\Data\Entities;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="preview",    options={"collate"="utf8_czech_ci"})
 */
class PreviewDAO extends \Kdyby\Doctrine\Entities\BaseEntity {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="path", type="string")
     */
    protected $path;


    /**
     * @ORM\Column(name="note", type="string")

     */
    protected $note;

    function __construct($path, $note=NULL){
	$this->note = $note;
	$this->path = $path;
    }


    public function getNote() {
	return $this->note;
    }

    public function getPath() {
	return $this->path;
    }
    
    
    public function setNote($note) {
	$this->note = $note;
    }

    public function setPath($path) {
	$this->path = $path;
    }


}
