<?php

namespace App\Presenters;

use Nette;
use App\Model;

class HomepagePresenter extends BasePresenter {

    /** @var Model\User @inject */
    public $user;

    /** @var Model\Material @inject */
    public $material;

    /** @var Model\Homepage @inject */
    public $homepage;

    public function renderDefault() {
	if ($this->user->isLoggedIn()) {
	    $this->template->userStatus = "uživatel s ID " . $this->user->getId() . " je přihlášen";
	} else {
	    $this->template->userStatus = "uživatel není přihlášen";
	}
	$this->template->imageId = rand(1, 3);
	$this->template->databaseData = $this->homepage->getData();
    }

}
