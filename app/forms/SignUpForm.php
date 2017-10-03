<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Utils\Random;

class SignUpForm extends Nette\Object {

    const PASSWORD_MIN_LENGTH = 7;

    /** @var FormFactory */
    private $factory;

    public function __construct(FormFactory $factory) {
		$this->factory = $factory;
    }

    public function create() {
		$form = $this->factory->create();
		$form->elementPrototype->addAttributes(array('class' => 'registration'));
		$form->addEmail('email', 'Váš e-mail:')
			->setRequired('Prosím zadejte email.')
			->setAttribute('placeholder', 'email');
		$form->addText('degree', 'Váš titul:')
			->setAttribute('placeholder', 'titul');
		$form->addText('name', 'Vaše jméno:')
			->setRequired('Prosím zadejte jméno.')
			->setAttribute('placeholder', 'jméno');
		$form->addText('surname', 'Vaše příjmení:')
			->setRequired('Prosím zadejte příjmení.')
			->setAttribute('placeholder', 'příjmení');
		$form->addPassword('password', 'Zvolte si heslo:')
			->setOption('description', sprintf('alespoň %d znaků', self::PASSWORD_MIN_LENGTH))
			->setRequired('Prosím zadejte heslo.')
			->setAttribute('placeholder', 'heslo')
			->addRule($form::MIN_LENGTH, NULL, self::PASSWORD_MIN_LENGTH);
		$form->addPassword('password2', 'Opět heslo:')
			->setRequired('Prosím zadejte heslo znovu.')
			->setAttribute('placeholder', 'heslo opět')
			->addConditionOn($form["password"], Form::FILLED)
			->addRule(Form::EQUAL, "Hesla se musí shodovat!", $form["password"]);


        $form->addHidden('link', Random::generate(10));
		$form->addSubmit('send', 'Uložit');
		return $form;
    }
}
