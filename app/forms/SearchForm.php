<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;

class SearchForm extends Nette\Object { /*zatim neni v config ani injektnuta v search.php todo */

    /** @var FormFactory */
    private $factory;

    public function __construct(FormFactory $factory) {
		$this->factory = $factory;
    }

    public function create() {
		$form = $this->factory->create();
        /*todo*/
		return $form;
    }

}
