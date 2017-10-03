<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Utils\Random;

class ForgottenPasswordForm extends Nette\Object {

    const PASSWORD_MIN_LENGTH = 7;

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

        $form->addHidden('newpass', Random::generate(10));
        $form->addSubmit('send', 'Zaslat e-mail');
        return $form;
    }
}
