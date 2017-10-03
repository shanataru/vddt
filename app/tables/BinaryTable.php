<?php

namespace App\Tables;

use Nette;
use Nextras\Datagrid\Datagrid;

class BinaryTable extends Nette\Object {

    /** @var TableFactory */
    private $grid;

    public function __construct(TableFactory $grid) {
	$this->grid = $grid;
    }

    //použitelné na profilu, navalí template pro profilovou stránku
    public function createByUser() {
	$grid = $this->grid->create();
	$grid->addColumn('binary', 'Jméno');
	$grid->addColumn('size', 'Velikost');
	$grid->addColumn('note', 'Popis');
	$grid->addColumn('language', 'Jazyk');
	$grid->addColumn('public', 'Veřejné')->enableSort($grid::ORDER_ASC);

	$grid->addCellsTemplate(__DIR__ . '/../templates/Grid/binaries.latte');
	return $grid;
    }

    //... no a podobné ostatní funkce...
}

class BinaryItem {

    public $binary;
    public $binaryId;
public $note;
    public $size;
    public $language;
    public $public;

    public function __construct($binary) {
	$this->binary = $binary->name;
	$this->binaryId = "";
	$this->note = $binary->note;

	$this->size = $binary->fileSize;
	$this->language = $binary->language;
	$this->public = $binary->public;
    }

}
