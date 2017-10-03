<?php

namespace App\Model;

use Nette;
use App\Data as AD;
use App\Tables as AT;
use App\Model\Model as AM;

class Product  extends AM{

    /** @var AD\Products */
    private $productsFacade;

    /** @var AT\ProductTable */
    private $productTable;

    public function __construct(AD\Products $products, AT\ProcessingTable $productTable) {
	$this->productsFacade = $products;
	$this->productTable = $productTable;
    }

}
