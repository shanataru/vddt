<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Utils\Random;

class ChangePasswordForm extends Nette\Object {

    const PASSWORD_MIN_LENGTH = 7;

    /** @var FormFactory */
    private $factory;


    public function __construct(FormFactory $factory) {
        $this->factory = $factory;
    }

    public function create() {
        $form = $this->factory->create();
        $form->addPassword('password', 'Zvolte si nové heslo:')
            ->setOption('description', sprintf('alespoň %d znaků', self::PASSWORD_MIN_LENGTH))
            ->setRequired('Prosím zadejte heslo.')
            ->setAttribute('placeholder', 'heslo')
            ->addRule($form::MIN_LENGTH, NULL, self::PASSWORD_MIN_LENGTH);
        $form->addPassword('password2', 'Opět heslo:')
            ->setRequired('Prosím zadejte nové heslo znovu.')
            ->setAttribute('placeholder', 'heslo opět')
            ->addConditionOn($form["password"], Form::FILLED)
            ->addRule(Form::EQUAL, "Hesla se musí shodovat!", $form["password"]);

        //$form->addHidden('newpass', $this->newpass);
        $form->addSubmit('send', 'Změnit heslo');
        return $form;
    }
}
