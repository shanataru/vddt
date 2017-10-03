<?php

namespace App\Presenters;

use Nette;
use App\Model;

class MaskPresenter extends BasePresenter {

    /** @var Model\Mask @inject */
    public $mask;

    /** @var Model\Processing @inject */
    public $processing;
    
    /** @var Model\Material @inject */
    public $material;

    public function renderDetail($maskId)
    {
        $this->template->maskData = $this->mask->getData($maskId);
        $this->template->maskId = $maskId;
    }

    public function renderDelete($maskId)
    {
        $this->mask->checkPermission($maskId);
        $this->template->maskData = $this->mask->getData($maskId);
        $this->template->maskId = $maskId;
    }
    
    public function renderModify($maskId)
    {
        $this->mask->checkPermission($maskId);
        $this->template->maskData = $this->mask->getData($maskId);
        $this->template->maskId = $maskId;
    }

    public function actionDeleteComplete($maskId)
    {
        $this->mask->delete($maskId);
        $this->flashMessage('Maska byla úspěšně smazána.');
        $this->getPresenter()->redirect('Profile:detail');
    }

    public function createComponentAddMaskForm() {
	return $this->mask->addMaskForm();
    }
    
    public function createComponentModifyMaskForm() {
        $maskId = $this->getParameter('maskId');
	return $this->mask->modifyMaskForm($maskId);
    }

    public function createComponentMaskProcessingGrid() {
        $maskId = $this->getParameter('maskId');
	return $this->processing->tableByMask($maskId);
    }
    
    public function createComponentMaskMaterialGrid() {
        $maskId = $this->getParameter('maskId');
	return $this->material->tableByMask($maskId);
    }

}
