<?php

namespace App\Presenters;

use Nette;
use App\Model;

class MaterialPresenter extends BasePresenter {

    /** @var Model\Material @inject */
    public $material;

    /** @var Model\Processing @inject */
    public $processing;
    
        /** @var Model\Mask @inject */
    public $mask;

    public function renderDetail($materialId) {
		$this->template->materialData = $this->material->getData($materialId);
		$this->template->materialId = $materialId;
    }
	
	public function renderDelete($materialId) {
		$this->material->checkPermission($materialId);
		$this->template->materialData = $this->material->getData($materialId);
		$this->template->materialId = $materialId;
    }
    
    public function renderModify($materialId) {
        $this->material->checkPermission($materialId);
        $this->template->materialData = $this->material->getData($materialId);
		$this->template->materialId = $materialId;
    }
	
	public function actionDeleteComplete($materialId) {
		$this->material->delete($materialId);
        $this->flashMessage('Materiál byl úspěšně smazán.');
		$this->getPresenter()->redirect('Profile:detail');
	}

    public function createComponentAddMaterialForm() {
		return $this->material->addMaterialForm();
    }

    public function createComponentModifyMaterialForm() {
		$materialId = $this->getParameter('materialId');
		return $this->material->modifyMaterialForm($materialId);
    }

    public function createComponentMaterialProcessingGrid() {
		$materialId = $this->getParameter('materialId');
		return $this->processing->tableByMaterial($materialId);
    }

    public function createComponentMaterialMaskGrid() {
		$materialId = $this->getParameter('materialId');
		return $this->mask->tableByMaterial($materialId);
    }

}
