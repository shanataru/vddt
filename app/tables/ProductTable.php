<?php

namespace App\Tables;

use Nette;

class ProductTable extends Nette\Object {

    /** @var TableFactory */
    private $grid;

    public function __construct(TableFactory $grid) {
		$this->grid = $grid;
    }
	
	//použitelné na profilu, navalí template pro profilovou stránku
	public function createByUser($username){
		$grid = $this->grid->create();
        $grid->addColumn('name', 'Jméno');
        //$grid->addColumn('dateStart', 'Start date');
        $grid->addColumn('state', 'Status');
        $grid->addColumn('author', 'Autor');
        //$grid->addColumn('dateEnd', 'End date');
		return $grid;	
	}
	
	//... no a podobné ostatní funkce...
}
