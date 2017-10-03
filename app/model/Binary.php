<?php

namespace App\Model;

use Nette;
use Nette\Security\User;
use App\Forms as FO;
use App\Data as AD;
use App\Tables as AT;
use Nette\Application\UI\Form;
use App\Model\Model as AM;
use Nette\Utils\Paginator;

class Binary extends AM {

    /** @var FO\MaterialForm */
    private $binaryForm;

    /** @var AD\Binaries */
    private $binaryFacade;

    /** @var AD\Users */
    private $userFacade;

    /** @var AT\BinaryTable */
    private $binaryTable;

    /** @var AD\Processings */
    private $processingFacade;


	/* --------------------------------------------------------------------
     * PUBLIC Konstruktor
     * -------------------------------------------------------------------- */


    public function __construct(FO\BinaryForm $binaryForm, AD\Processings $processings, AD\Binaries $binaries, AD\Users $users, AT\BinaryTable $binaryTable, User $user)
    {
        parent::__construct($user);
        $this->binaryForm = $binaryForm;
        $this->binaryFacade = $binaries;
        $this->userFacade = $users;
        $this->binaryTable = $binaryTable;
        $this->processingFacade = $processings;
        $this->user = $user;
    }


	/* --------------------------------------------------------------------
     * PUBLIC  Basic metoda pro získání informací o binárce
     * -------------------------------------------------------------------- */


    public function getData($binaryId)
    {
        $data = [];
        $binary = $this->binaryFacade->findById($binaryId);
        $data["filesize"] = $this->filesizeFormatted($binary->size);
        $selection = $this->processingFacade->findAllByBinary($binaryId);
        $data["countProcessing"] = $this->countVisible($selection, $this->user->id);
        $data["filename"] = $binary->name;
        $data["language"] = $binary->language;
        $data["privacy"] = $binary->public ? "veřejné" : "soukromé";
        $data["uploadTime"] = $binary->uploadedAt()->format('d.m.Y H:i');
        $author = $this->userFacade->findById($binary->getAuthorId());
        $data["author"] = $author->getName();
        $data["note"] = $binary->note;
        $data["owner"] = $this->checkSession($binary->author);
        return $data;
    }


    /* --------------------------------------------------------------------
     * Nahrání souboru binárky a databaze
     * -------------------------------------------------------------------- */


    private function createBinary($values, $fileName)
    {
        $originalPath = parent::BINARYPATH . $fileName;
        $path = parent::BINARYPATHSHORT . $fileName;
        $fileSize = filesize($originalPath);
        $userId = $this->user->id;

        $this->binaryFacade->makeNew($values["name"], $userId, $path, $values["privacy"], $fileSize,$values["note"], $values["language"]);
    }

    private function uploadBinary($file)
    {
        $format = $this->getFormat($file);
        $name = $this->createName($format);
        $this->moveFile($file, "binary", $name);
        return $name;
    }


    /* --------------------------------------------------------------------
     * Úprava dat binarky (změna záznamu binarky)
     * -------------------------------------------------------------------- */
    
    
    /* Předá datové vrstvě informace potřebné pro změnu záznamu */
    private function modifyBinaryData($values, $binary)
    {
        $binary->setName($values['name']);
        if ($values['privacy']) {
            $binary->makePublic();
        } else {
            $binary->makePrivate();
        }
        $binary->setNote($values['note']);
    }


    /* --------------------------------------------------------------------
     * PUBLIC Přidání binárky (form i zpracování)
     * -------------------------------------------------------------------- */


    public function addBinary(Form $form, $values)
    {
        if ($values["file"] == NULL or !$values["file"]->isOk())
        {
            throw "Nahrávání binárky selhalo.";
        }

        $fileName = $this->uploadBinary($values["file"]);
        $this->createBinary($values, $fileName);
    }


    /* nahrávání materiálu */

    public function addBinaryForm()
    {
		$form = $this->binaryForm->create();
		$form->onSuccess[] = array($this, 'addBinary');
		return $form;
    }

    /* --------------------------------------------------------------------
     * PUBLIC Úprava binárky (form i zpracování)
     * -------------------------------------------------------------------- */


    public function modifyBinary(Form $form, $values)
    {
        $binaryId = $form->getPresenter()->getParameter('binaryId');
        $binary = $this->binaryFacade->findById($binaryId);
        $this->modifyBinaryData($values, $binary);
        $this->binaryFacade->itemEdited();
        $form->getPresenter()->redirect('Binary:detail', array("binaryId" => $binary->id));
    }

    /* Vytvoří formulář pro úpravu informací o binarce */
    public function modifyBinaryForm($binaryId)
    {
        $binary = $this->binaryFacade->findById($binaryId);
        if (!$binary)
        {
            throw new BadRequestException;
        }
        $form = $this->binaryForm->modify($binary);
        $form->onSuccess[] = array($this, 'modifyBinary');
        return $form;
    }


    /* Pokud není přihlášený uživatel vlastník masky, funkce vyhodí výjimku */
    public function checkPermission($binaryId)
    {
        $binary = $this->binaryFacade->findById($binaryId);
        if (!$binary)
        {
            throw new BadRequestException;
        }
        if (!$this->checkSession($binary->author))
        {
            throw 'Nedostatečná práva';
        }

    }


    /* --------------------------------------------------------------------
     * PUBLIC Tabulky
     * -------------------------------------------------------------------- */


    public function prepareDataAll($order, Paginator $paginator = NULL) {
	$user = $this->user->id;
	$selection = $this->binaryFacade->findAllByAuthor($user, $order, $paginator);

	$gridSelection = [];

	foreach ($selection as $item) {
	    $gridItem = new AT\BinaryItem($item);
	    $gridItem->binaryId = $item->id;

	    //--------public state------------
	    if ($gridItem->public) {
		$gridItem->public = 'ano';
	    } else {
		$gridItem->public = 'ne';
	    }
	    if ($gridItem->size) {
		$gridItem->size = $this->filesizeFormatted($gridItem->size);
	    }

	    $gridSelection[] = $gridItem;
	}

	return $gridSelection;
    }

    public function getDataAll($filter, $order, Paginator $paginator = NULL) {
	$selection = $this->prepareDataAll($order, $paginator);
	return $selection;
    }

    public function getDataSourceSum($filter, $order) {
	$count = $this->binaryFacade->countByAuthorId($this->user->id);
	return $count;
    }

    public function tableByUser() {
	$grid = $this->binaryTable->createByUser();
	$grid->setDataSourceCallback([$this, 'getDataAll']);
	$grid->setPagination(4, [$this, 'getDataSourceSum']);
	return $grid;
    }

}
