<?php

namespace App\Forms;

use Nette;
use Kdyby;
use Nette\Application\UI\Form;

class BinaryForm extends Nette\Object {

    /** @var FormFactory */
    private $factory;

    public function __construct(FormFactory $factory) {
        $this->factory = $factory;
    }



    private function createFormBase()
    {
        $form = $this->factory->create();
        $form->addText('name', 'Jméno binárky')
                ->setRequired('Prosím zadejte jméno.')
                ->setAttribute('placeholder', 'jméno');
        $form->addText('language', 'Programovací jazyk')
                ->setRequired('Prosím zadejte programovací jazyk.')
                ->setAttribute('placeholder', 'C++');
        $privacy = [
            0 => 'souromé',
            1 => 'veřejné',
        ];
        $form->addRadioList('privacy', 'Soukromí', $privacy)
                ->getSeparatorPrototype()->setName(NULL);
        $author = [
            1 => 'přidat jméno autora',
            0 => 'jméno autora nezobrazovat',
        ];
        $form->addTextArea('note', 'Poznámka')
                ->setRequired(FALSE) // nepovinná
                ->addRule(Form::MAX_LENGTH, 'Poznámka je příliš dlouhá', 1000)
                ->setAttribute('placeholder', 'přidejte poznámku');
        return $form;


    }

    private function setupDefaults($form)
    {
        $form['privacy']->setDefaultValue(0);
    }

    public function setupForModify($form, $binary)
    {
        $form['name']->setDefaultValue($binary->getName());
        $form['language']->setDefaultValue($binary->getLanguage());
        $form['privacy']->setDefaultValue($binary->isPublic() ? 1 : 0);
        $form['note']->setDefaultValue($binary->getNote());
    }

    public function create()
    {
        $form = $this->createFormBase();
        $this->setupDefaults($form);
        $form->addUpload('file', 'Binárka')
                ->setRequired('Prosím nahrajte zkompilovanou binárku.')
                ->addRule(Form::MAX_FILE_SIZE, 'Maximální velikost souboru je 64 MiB.', 64 * 1024 * 1024 /* v bytech */);
        $form->addSubmit('send', 'Nahrát');
        return $form;
    }

    public function modify($binary)
    {
        $form = $this->createFormBase();
        $form->addSubmit('send', 'Uložit úpravy');
        $this->setupForModify($form, $binary);
        return $form;
    }

}
