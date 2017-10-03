<?php

namespace App\Presenters;

use Nette;
use App\Model;

class ProcessingPresenter extends BasePresenter {

    /** @var Model\Processing @inject */
    public $processing;

    public function renderDetail($processingId) {
        $this->template->maskData = $this->processing->getData($processingId);
    }

}
