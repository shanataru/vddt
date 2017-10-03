<?php

namespace App\Data\Entities;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="take",    options={"collate"="utf8_czech_ci"})
 */
class CameraTakeDAO extends \Kdyby\Doctrine\Entities\BaseEntity {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="take", type="string")
     */
    protected $take;

    function __construct($take)
    {
	   $this->take = $take;
    }

    public function getTake()
    {
	   return $this->take;
    }
    
    public function setTake($take)
    {
	   $this->take = $take;
    }

}
