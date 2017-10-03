<?php

namespace App\Tables;

use Nette;
use Nextras\Datagrid\Datagrid;

//! Poznámka - budou gridy tahat data z databáze tady a nebo až v modelu? 
//Byl bych pro model, protože tam už koneckonců to napojení na fasádu je kvůli ostatním věcem 
//a jedná se o jednu řádku, která k tomu prostě jen připojí funkci... viz Processing.php

class ProcessingTable extends Nette\Object {

    /** @var TableFactory */
    private $grid;

    public function __construct(TableFactory $grid) {
	$this->grid = $grid;
    }

    //použitelné na profilu, navalí template pro profilovou stránku
    public function createByUser() {//nemusi tu byt id uzivatele
	$grid = $this->grid->create();
	$grid->addColumn('material', 'Materiál');
	$grid->addColumn('binary', 'Binárka');
	$grid->addColumn('mask', 'Maska');
	$grid->addColumn('dateStart', 'Datum')->enableSort();
	$grid->addColumn('status', 'Stav')->enableSort($grid::ORDER_ASC);
	$grid->addColumn('public', 'Veřejné')->enableSort();

	$grid->addCellsTemplate(__DIR__ . '/../templates/Grid/processings.latte');
	return $grid;
    }

    public function createByMaterial() {
	$grid = $this->grid->create();
	$grid->addColumn('processing', 'Jméno');
	$grid->addColumn('author', 'Autor');
	$grid->addColumn('binary', 'Binárka');
	$grid->addColumn('mask', 'Maska');
	$grid->addColumn('dateStart', 'Datum')->enableSort();
	$grid->addColumn('status', 'Stav')->enableSort($grid::ORDER_ASC);

	$grid->addCellsTemplate(__DIR__ . '/../templates/Grid/materialprocessings.latte');
	return $grid;
    }

    public function createByMask() {
	$grid = $this->grid->create();
	$grid->addColumn('processing', 'Jméno');
	$grid->addColumn('author', 'Autor');
	$grid->addColumn('material', 'Materiál');
	$grid->addColumn('binary', 'Binárka');
	$grid->addColumn('dateStart', 'Datum')->enableSort();
	$grid->addColumn('status', 'Stav')->enableSort($grid::ORDER_ASC);

	$grid->addCellsTemplate(__DIR__ . '/../templates/Grid/maskprocessings.latte');
	return $grid;
    }

    public function createByBinary() {
	$grid = $this->grid->create();
	$grid->addColumn('processing', 'Jméno');
	$grid->addColumn('author', 'Autor');
	$grid->addColumn('material', 'Materiál');
	$grid->addColumn('mask', 'Maska');
	$grid->addColumn('dateStart', 'Datum')->enableSort();
	$grid->addColumn('status', 'Stav')->enableSort($grid::ORDER_ASC);

	$grid->addCellsTemplate(__DIR__ . '/../templates/Grid/binaryprocessings.latte');
	return $grid;
    }

    //... no a podobné ostatní funkce...
}

class ProcessingItem {

    public $processingId;
    public $processing;
    public $material;
    public $materialId;
    public $binary;
    public $binaryId;
    public $mask;
    public $maskId;
    public $dateStart;
    public $status;
    public $statusNo;
    public $public;
    public $author;

    public function __construct($processing) {
	$this->processingId = $processing->id;
	$this->processing = $processing->name;
	$this->author = "";
	$this->material = "-";
	$this->materialId = "";
	$this->binary = "-";
	$this->binaryId = "";
	$this->mask = "-";
	$this->maskId = "";
	$this->statusNo = $processing->status;
	$this->dateStart = $processing->dateStart;
	$this->status = $processing->status;
	$this->public = $processing->public;
    }

}
