<?php

namespace App\Presenters;
use Nette;
use App\Model;

class ProductPresenter extends BasePresenter {
	
	/** @var Model\Product @inject */
	public $product;
    
    public function renderDetail($productId) {
		echo $productId;
    }
	
	public function createComponentAddMaterialForm() {
		return $this->product->addMaterialForm();
    }
    
}
