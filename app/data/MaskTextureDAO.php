<?php

namespace App\Data\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="mask_texture",    options={"collate"="utf8_czech_ci"})
 */
class MaskTextureDAO extends \Kdyby\Doctrine\Entities\BaseEntity {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="texture_id", type="integer")
     */
    protected $texture;

    /**
     * @ORM\Column(name="mask_id", type="integer")
     */
    protected $mask;

    function __construct($m, $t) {
	$this->mask = $m;
	$this->texture = $t;
    }

    public function getTextureId() {
	return $this->texture;
    }

    public function getMaskId() {
	return $this->mask;
    }

}
