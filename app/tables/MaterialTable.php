<?php

namespace App\Tables;

use Nette;

class MaterialTable extends Nette\Object {

    /** @var TableFactory */
    private $grid;

    public function __construct(TableFactory $grid) {
		$this->grid = $grid;
    }


    public function createByUser()
    {
		$grid = $this->grid->create();
		$grid->addColumn('previewPath', 'Náhled');
		$grid->addColumn('texturename', 'Jméno')->enableSort();
		$grid->addColumn('dateUpload', 'Datum')->enableSort();
		$grid->addColumn('size', 'Velikost')->enableSort();
		$grid->addColumn('public', 'Veřejné')->enableSort($grid::ORDER_ASC);

		$grid->addCellsTemplate(__DIR__ . '/../templates/Grid/materials.latte');
		return $grid;
    }

    public function createByMask()
    {
		$grid = $this->grid->create();
		$grid->addColumn('previewPath', 'Náhled');
		$grid->addColumn('texturename', 'Jméno')->enableSort($grid::ORDER_ASC);
		$grid->addColumn('author', 'Autor');
		$grid->addColumn('dateUpload', 'Datum');
		$grid->addColumn('size', 'Velikost');
		$grid->addColumn('public', 'Veřejné');

		$grid->addCellsTemplate(__DIR__ . '/../templates/Grid/maskmaterials.latte');
		return $grid;
    }

}

class MaterialItem
{

    public $id;
    public $texturename;
    public $previewId;
    public $previewPath;
    public $size;
    public $authorId;
    public $author;
    public $note;
    public $public;
    public $dateUpload;

    public function __construct($material)
    {
		$this->texturename = $material->name;
		$this->id = $material->id;
		$this->previewId = $material->preview;
		$this->previewPath = "";
		$this->author = "";
		$this->authorId = $material->author;
		$this->size = $material->size;
		$this->note = $material->note;
		$this->public = $material->public;
		$this->dateUpload = $material->dateUpload;
    }

}
