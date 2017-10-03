<?php

namespace App\Presenters;

use Nette;
use App\Model;

class ProfilePresenter extends BasePresenter {

    /** @var Model\User @inject */
    public $user;

    /** @var Model\Processing @inject */
    public $processing;

    /** @var Model\Binary @inject */
    public $binary;

    /** @var Model\Mask @inject */
    public $mask;

    /** @var Model\Material @inject */
    public $material;

    /* id zobrazovaného uživatele */
    private $userPageId;

    public function renderDetail($userId) {
	$this->userPageId = $userId;
	$this->template->userData = $this->user->getData($userId);
    }
	
	public function renderModify($userId) {
	$this->userPageId = $userId;
	$this->template->userData = $this->user->getData($userId);
    }
    
    public function createComponentProcessingGrid() {
	return $this->processing->tableByUser($this->userPageId);
    }

    public function createComponentMaskGrid() {
	$userID = $this->userPageId;
	return $this->mask->tableByUser($userID);
    }

    public function createComponentMaterialGrid() {
	$userID = $this->userPageId;
	return $this->material->tableByUser($userID);
    }

    public function createComponentBinaryGrid() {
	$userID = $this->userPageId;
	return $this->binary->tableByUser($userID);
    }

    protected function createComponentModifyProfileForm() {
        $form = $this->user->modifyProfileForm();
        $form->onSuccess[] = array($this, 'redirectProfile');
        return $form;
    }

    public function redirectProfile($form) {
        // po uspěšném přihlášení přesměrujeme na homepage
        $this->getPresenter()->redirect('Profile:detail');
    }

}
