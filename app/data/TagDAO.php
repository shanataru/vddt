<?php

namespace App\Data\Entities;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tag",    options={"collate"="utf8_czech_ci"})
 */
class TagDAO extends \Kdyby\Doctrine\Entities\BaseEntity {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="name", type="string")
     */
    protected $name;

    function __construct($name){
	$this->name = $name;
    }

    public function getName() {
	return $this->name;
    }
    
    public function setName($name) {
	$this->name = $name;
    }

}
