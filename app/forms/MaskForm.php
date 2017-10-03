<?php

namespace App\Forms;

use Nette;
use Kdyby;
use Nette\Application\UI\Form;

class MaskForm extends Nette\Object {

    /** @var FormFactory */
    private $factory;

    public function __construct(FormFactory $factory) {
        $this->factory = $factory;
    }

    private function createFormBase() {
        $form = $this->factory->create();
        $form->addText('name', 'Jméno masky')
                ->setRequired('Prosím zadejte jméno.')
                ->setAttribute('placeholder', 'jméno');

        $usage = [
            'universal' => 'univerzální maska',
            'custom' => 'přiřadit k materiálům',
        ];
        $radioSetup = $form->addRadioList('usage', 'Univerzální maska', $usage);
        $radioSetup->getSeparatorPrototype()->setName(NULL);
        $radioSetup->addCondition($form::EQUAL, 'custom')
                ->toggle('material'); //když je custom, tak se zobrazí sekce s ID material-selection

        $form->addText('material', 'ID materiálu');

        $privacy = [
            0 => 'souromé',
            1 => 'veřejné',
        ];
        $form->addRadioList('privacy', 'Soukromí', $privacy)
                ->getSeparatorPrototype()->setName(NULL);
        $author = [
            1 => 'zobrazit jméno autora',
            0 => 'jméno autora nezobrazovat',
        ];
        $form->addRadioList('showAuthor', 'Autorství', $author)
                ->getSeparatorPrototype()->setName(NULL);
        $form['showAuthor']->setDefaultValue(0);
        $form->addTextArea('note', 'Poznámka')
                ->setRequired(FALSE) // nepovinná
                ->addRule(Form::MAX_LENGTH, 'Poznámka je příliš dlouhá', 1000)
                ->setAttribute('placeholder', 'přidejte poznámku');
        
        return $form;
    }
    
    private function setupDefaults($form){
        $form['usage']->setDefaultValue('universal');
        $form['privacy']->setDefaultValue(0);
    }
    
    private function setupForModify($form, $mask, $materials){
        $form['name']->setDefaultValue($mask->getName());
        $form['usage']->setDefaultValue($mask->isUniversal() ? 'universal' : 'custom');
        $form['privacy']->setDefaultValue($mask->isPublic() ? 1 : 0);
        $form['showAuthor']->setDefaultValue(0);
        $form['note']->setDefaultValue($mask->getNote());

        $materialIDs = [];
        foreach ($materials as $material)
        {
            $materialIDs[] = $material->id;
        }
        $materialString = implode (', ' , $materialIDs);
        $form['material']->setDefaultValue($materialString);

    }

    public function create() {
        $form = $this->createFormBase();
        $form->addUpload('file', 'Media')
                ->setRequired('Prosím nahrajte masku (foto/video).')
                ->addRule(Form::MAX_FILE_SIZE, 'Maximální velikost souboru je 64 MiB.', 64 * 1024 * 1024 /* v bytech */);
        $form->addSubmit('send', 'Nahrát');
        $this->setupDefaults($form);
        return $form;
    }
    
    public function modify($mask, $materials) {
        $form = $this->createFormBase();
        $this->setupForModify($form, $mask, $materials);
        $form->addSubmit('send', 'Uložit');
        return $form;
    }

}
