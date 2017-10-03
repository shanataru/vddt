<?php

namespace App\Tables;

use Nette;

class MaskTable extends Nette\Object {

    /** @var TableFactory */
    private $grid;

    public function __construct(TableFactory $grid) {
	$this->grid = $grid;
    }

    //použitelné na profilu, navalí template pro profilovou stránku
    public function createByUser() {
	$grid = $this->grid->create();
	$grid->addColumn('maskName', 'Jméno');
	$grid->addColumn('size', 'Velikost');
	$grid->addColumn('note', 'Popis');
	$grid->addColumn('universal', 'Univerzální')->enableSort();
	$grid->addColumn('public', 'Veřejné')->enableSort($grid::ORDER_ASC);

	$grid->addCellsTemplate(__DIR__ . '/../templates/Grid/masks.latte');
	return $grid;
    }

    //... no a podobné ostatní funkce...

    public function createByMaterial() {
	$grid = $this->grid->create();
	$grid->addColumn('maskName', 'Jméno')->enableSort($grid::ORDER_ASC);
	$grid->addColumn('author', 'Autor');
	$grid->addColumn('size', 'Velikost');
	$grid->addColumn('note', 'Popis');
	$grid->addColumn('public', 'Veřejné');

	$grid->addCellsTemplate(__DIR__ . '/../templates/Grid/materialmasks.latte');
	return $grid;
    }

}

class MaskItem
{

    public $maskName;
    public $id;
    public $size;
    public $note;
    public $public;
    public $universal;
    public $authorId;
    public $author;

    public function __construct($mask)
    {
		$this->maskName = $mask->name;
		$this->id = $mask->id;
		$this->authorId = $mask->author;
		$this->dateUpload = $mask->dateUpload;
		$this->size = $mask->fileSize;
		$this->note = $mask->note;
		$this->public = $mask->public;
		$this->universal = $mask->universal;
    }

}
