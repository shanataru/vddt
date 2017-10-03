<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Utils\Random;

class ModifyProfileForm extends Nette\Object {


    /** @var FormFactory */
    private $factory;

    public function __construct(FormFactory $factory) {
        $this->factory = $factory;
    }

    private function setupForModify($form, $data){
        $form['degree']->setDefaultValue($data["degree"]);
        $form['name']->setDefaultValue($data["name"]);
        $form['surname']->setDefaultValue($data["surname"]);
        $form['email']->setDefaultValue($data["email"]);
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
        $form->addPassword('password', 'Potvrďte změny heslem:')
            ->setRequired('Prosím zadejte heslo.')
            ->setAttribute('placeholder', 'heslo');

        return $form;
    }

    public function modify($data){
        $form = $this->create();
        $this->setupForModify($form, $data);
        $form->addSubmit('send', 'Uložit');
        return $form;
    }
}
