<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;

class MaterialForm extends Nette\Object {

    /** @var FormFactory */
    private $factory;

    public function __construct(FormFactory $factory) {
        $this->factory = $factory;
    }
    
    private function createFormBase() {
        $form = $this->factory->create();
        $form->addText('name', 'Jméno materiálu')
                ->setRequired('Prosím zadejte jméno.')
                ->setAttribute('placeholder', 'jméno');
		
		$take = [
            'makro snímek' => 'makro snímek',
            'panorama' => 'panorama',
			'běžný snímek' => 'běžný snímek',
			'jiné' => 'jiné'
        ];
		
        $form->addSelect('take', 'Druh záběru', $take)
                ->setRequired('Prosím popište druh záběru.')
                ->setDefaultValue('běžný snímek')
                ->setAttribute('id', 'take');
		
		$move = [
            'statický' => 'statický',
            'plynulý horizontální' => 'plynulý horizontální',
			'plynulý vertikální' => 'plynulý vertikální',
			'plynulý všemi směry' => 'plynulý všemi směry',
			'třes horizontální' => 'třes horizontální',
			'třes vertikální' => 'třes vertikální',
			'třes všemi směry' => 'třes všemi směry'
        ];
		
        $form->addSelect('move', 'Pohyb kamery', $move)
                ->setRequired('Prosím popište pohyb kamery.')
				->setDefaultValue('statický')
                ->setAttribute('id', 'move');
		
        $form->addTextArea('tags', 'Tagy')
                ->setAttribute('placeholder', 'trávník, vlnění...')
                ->setAttribute('id', 'tags')
				->setRequired(FALSE) // nepovinná
                ->setAttribute('oninput', 'parse("tags", "tags-formatted")');
        
        $privacy = [
            0 => 'souromé',
            1 => 'veřejné',
        ];
        
        $form->addRadioList('privacy', 'Soukromí', $privacy)
             ->getSeparatorPrototype()->setName(NULL);
        
        $form->addTextArea('note', 'Poznámka')
                ->setRequired(FALSE) // nepovinná
                ->addRule(Form::MAX_LENGTH, 'Poznámka je příliš dlouhá', 1000)
                ->setAttribute('placeholder', 'přidejte poznámku');
        return $form;
    }
    
    private function setupDefaults($form) {
        $form['privacy']->setDefaultValue(0);
    }
    
    private function setupForModify($form, $material, $take, $move, $tags){
        $form['name']->setDefaultValue($material->getName());
        if ($take) {
            $form['take']->setDefaultValue($take->getTake());
        }
        if ($move) {
            $form['move']->setDefaultValue($move->getMove());
        }
        $tagString = implode ( ', ' , $tags );     
        $form['tags']->setDefaultValue($tagString);
        $form['privacy']->setDefaultValue($material->isPublic() ? 1 : 0);
        $form['note']->setDefaultValue($material->getNote());
    }

    public function create() {
        $form = $this->createFormBase();
        $form->addUpload('file', 'Media')
                ->setRequired('Prosím nahrajte foto/video.')
                ->addRule(Form::MAX_FILE_SIZE, 'Maximální velikost souboru je 64 MiB.', 64 * 1024 * 1024 /* v bytech */);
        $form->addSubmit('send', 'Nahrát');
        $this->setupDefaults($form);       
        return $form;
    }

    public function modify($material, $take, $move, $tags) {
        $form = $this->createFormBase();
        $form->addSubmit('send', 'Uložit úpravy');
        $this->setupForModify($form, $material, $take, $move, $tags);
        return $form;
    }

}
