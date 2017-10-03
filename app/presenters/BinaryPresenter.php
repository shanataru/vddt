<?php

namespace App\Presenters;

use Nette;
use App\Model;

class BinaryPresenter extends BasePresenter {

    /** @var Model\Binary @inject */
    public $binary;

    /** @var Model\Processing @inject */
    public $processing;  

    public function renderDetail($binaryId)
    {
        $this->template->binaryData = $this->binary->getData($binaryId);
        $this->template->binaryId = $binaryId;
    }

    public function renderModify($binaryId)
    {
        $this->binary->checkPermission($binaryId);
        $this->template->binaryData = $this->binary->getData($binaryId);
        $this->template->binaryId = $binaryId;
    }

    public function createComponentAddBinaryForm()
    {
	   return $this->binary->addBinaryForm();
    }

    public function createComponentModifyMaskForm()
    {
        $binaryId = $this->getParameter('binaryId');
        return $this->binary->modifyBinaryForm($binaryId);
    }

    public function createComponentBinaryProcessingGrid()
    {
        $binaryId = $this->getParameter('binaryId');
        return $this->processing->tableByBinary($binaryId);
    }

}
