<?php

namespace App\Data\Entities;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="move",    options={"collate"="utf8_czech_ci"})
 */
class CameraMoveDAO extends \Kdyby\Doctrine\Entities\BaseEntity {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="move", type="string")
     */
    protected $move;

    function __construct($move){
	$this->move = $move;
    }

    public function getMove() {
	return $this->move;
    }
    
    public function setMove($move) {
	$this->move = $move;
    }

}
