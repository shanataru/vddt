<?php

namespace App\Data\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="texture_tag",    options={"collate"="utf8_czech_ci"})
 */
class TagTextureDAO extends \Kdyby\Doctrine\Entities\BaseEntity {

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
     * @ORM\Column(name="tag_id", type="integer")
     */
    protected $tag;

    function __construct($tag, $texture) {
	$this->tag = $tag;
	$this->texture = $texture;
    }

    public function getTextureId() {
	return $this->texture;
    }

    public function getTagId() {
	return $this->tag;
    }

}
