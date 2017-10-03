<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;

class SignInForm extends Nette\Object {

    /** @var FormFactory */
    private $factory;

    public function __construct(FormFactory $factory) {
		$this->factory = $factory;
    }

    public function create() {
		$form = $this->factory->create();
		$form->addText('email', 'Email:')
			->setRequired('Prosím zadejte váš email.')
			->setAttribute('placeholder', 'email');
		$form->addPassword('password', 'Heslo:')
			->setRequired('Prosím zadejte vaše heslo.')
			->setAttribute('placeholder', 'heslo');
		$form->addSubmit('send', 'Přihlásit');
		return $form;
    }

}
