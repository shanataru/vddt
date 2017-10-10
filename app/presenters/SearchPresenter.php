<?php

namespace App\Presenters;

use Nette;
use App\Model;

class SearchPresenter extends BasePresenter {

    /** @var Model\Search @inject */
    public $search;


    public function renderResults() {
        /*todo*/
    }


    protected function createComponentSearchForm() {
        $form = $this->search->searchForm();
        $form->onSuccess[] = array($this, 'showResults');
        return $form;
    }

    public function showResults($form) {
        // po zadani parametru ukazeme vysledky
        $this->getPresenter()->redirect('Search:results');
    }


}
