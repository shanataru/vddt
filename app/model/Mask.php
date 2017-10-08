<?php

namespace App\Model;

use Nette;
use Nette\Security\User;
use App\Forms as FO;
use App\Data as AD;
use App\Tables as AT;
use Nette\Application\UI\Form;
use Nette\Utils\Image;
use App\Model\Model as AM;
use Nette\Utils\Paginator;

class Mask extends AM {

    /** @var FO\MaterialForm */
    private $maskForm;

    /** @var AD\Processings */
    private $maskFacade;

    /** @var AD\Materials */
    private $materialFacade;

    /** @var AD\Previews */
    private $previewFacade;

    /** @var AD\Processings */
    private $processingFacade;

    /** @var AT\MaskTable */
    private $maskTable;

    /** @var AD\Users */
    private $userFacade;

    private $materialId;

	/* --------------------------------------------------------------------
     * PUBLIC Konstruktor
     * -------------------------------------------------------------------- */


    public function __construct(FO\MaskForm $maskForm, AD\Masks $masks, AD\Materials $materials, AD\Processings $processings, AD\Previews $previews, AT\MaskTable $maskTable, AD\Users $users, User $user)
    {
    	parent::__construct($user);
		$this->maskForm = $maskForm;
		$this->maskFacade = $masks;
		$this->materialFacade = $materials;
		$this->processingFacade = $processings;
		$this->previewFacade = $previews;
		$this->maskTable = $maskTable;
		$this->userFacade = $users;
		$this->user = $user;
    }


	/* --------------------------------------------------------------------
     * PUBLIC Basic metoda pro získání informací o masce
     * -------------------------------------------------------------------- */


    public function getData($maskId)
    {
    	$data = [];
		$mask = $this->maskFacade->findById($maskId);
		$preview = $this->previewFacade->findById($mask->getPreviewId());
		$author = $this->userFacade->findById($mask->getAuthorId());
		$size = $this->filesizeFormatted(filesize(__DIR__ . "/../../www/" . $mask->getPath()));

		$selection = $this->processingFacade->findAllByMask($maskId);
        $data["countProcessing"] = $this->countVisible($selection, $this->user->id);
        $selection = $this->materialFacade->findAllByMask($maskId);
        $data["countMaterial"] = $this->countVisible($selection, $this->user->id);

        $data["usage"] = $mask->isUniversal() ? "univerzální maska" : "maska připojena k materiálu";
        $data["previewPath"] = $preview->getPath();
        $data["filename"] = $mask->getName();
        $data["dimensions"] = $mask->getWidth() . "×" . $mask->getHeight();
        $data["format"] =  $mask->getFormat();
        $data["filesize"] = $this->filesizeFormatted($mask->fileSize);
        $data["privacy"] =  ($mask->isPublic()) ? "veřejné" : "soukromé";
        $data["mediaType"] = $this->getMediaType($mask->getFormat());
        $data["uploadTime"] =  $mask->uploadedAt()->format('d.m.Y H:i');
        $data["author"] = $author->getName();
        $data["note"] = $mask->getNote();
        $data["owner"] = $this->checkSession($mask->author);

		return $data;
    }


    /* --------------------------------------------------------------------
     * Vytvoření materiálu (nahrání textury a náhledu)
     * -------------------------------------------------------------------- */
     
    
    /* Předá datové vrstvě potřebná data pro vytvoření záznamu materiálu a náhledu, nahraje náhled */
    private function createMask($values, $fileName)
    {     
	    $preview = $this->previewFacade->makeNew(parent::PREVIEWPATHSHORT . $fileName, NULL);
        $originalPath = parent::MASKPATH . $fileName;
        $path = parent::MASKPATHSHORT . $fileName;
        $fileSize = filesize($originalPath);
        $width; $height;

        if ($values["file"]->isImage())
        {
        	$image = Image::fromFile($originalPath);
        	$width = $image->width;
        	$height = $image->height;
        }

        $usage = $values["usage"];
        $extension = $this->getFormat($values["file"]);
        $user = $this->user->id;

	    $mask = $this->maskFacade->makeNew($values["name"], $height, $width, $extension, $user, $preview->id, $path, $values["usage"], $values["privacy"], $values["note"], $fileSize);
        return $mask;
    }


    /* --------------------------------------------------------------------
     * Přidání kontextu masky (naváže materiály)
     * -------------------------------------------------------------------- */
    
    /* ze stringu naprasuje id (zbaví se duplicit a ožeže whitespace), oddělovač je čárka */
    private function parseIds($ids)
    {
        $ids_array = array_filter(explode(',', $ids), 'strlen');
        $ids_trimmed = array_map('trim', $ids_array);
        $ids_clean_array = array_unique($ids_trimmed);
        return $ids_clean_array;
    }

    /* Vrátí pole id materiálů z materialIDsA, které nejsou mezi materialIDsB */
    private function filterMaterials($materialIDsA, $materialIDsB)
    {
        return array_diff($materialIDsA, $materialIDsB);    
    }

    /* Přidá všechny materiály z vloženého pole s jejich ID k dané masce */
    private function addNewMaterials($mask, $materialIDs)
    {
        foreach($materialIDs as $materialID)
        {
            $this->maskFacade->addMaskToTexture($mask->id, $materialID);
        }
    }
    
    /* Odebere masky danému materiálu */
    private function removeUnusedMaterials($mask, $materialIDs)
    {
        foreach($materialIDs as $materialID)
        {
            $this->maskFacade->removeMaskFromTexture($mask->id, $materialID);
        }
    }  

    /* Vrátí ID materiálů ke kterým je maska připojená */
    private function getUsedMaterialsIDs($mask)
    {
        $usedMaterials = $this->materialFacade->findAllByMask($mask->id);
        $usedMaterialsIDs = [];   
        foreach($usedMaterials as $material)
        {
            $usedMaterialsIDs[] = $material->id;
        }
        return $usedMaterialsIDs;
    }

    /* Přidá všechny nové tagy a vrátí pole neplatných tagů */
    private function addMaterials($mask, $newMaterialIDs)
    {
        $usedMaterialsIDs = $this->getUsedMaterialsIDs($mask);
        $filtredMaterialsIDs = $this->filterMaterials($newMaterialIDs, $usedMaterialsIDs);
        $this->addNewMaterials($mask, $filtredMaterialsIDs);
        return $this->filterMaterials($usedMaterialsIDs, $newMaterialIDs);     
    }

    /* Přidá masku k texturám */
    private function setMaskContext($values, $mask)
    {
        if ($values["usage"] == "custom")
        {
        	$materialIDs = $this->parseIds($values["material"]);
            $unusedMaterialsIDs = $this->addMaterials($mask, $materialIDs);
            $this->removeUnusedMaterials($mask, $unusedMaterialsIDs);
        }
        else
        {
            $unusedMaterialsIDs = $this->getUsedMaterialsIDs($mask);
            $this->removeUnusedMaterials($mask, $unusedMaterialsIDs);
        }
    }


    /* --------------------------------------------------------------------
     * Zpracování souborů při uploadu materiálu
     * -------------------------------------------------------------------- */

      
    /* Provede upload souborů a vrátí POUZE jméno souboru ve struktuře (ne cestu) na ftp serveru */
    private function uploadMask($file)
    {
        if ($file->isImage())
        {
            $format = $this->getFormat($file);
            $name = $this->createName($format);
            $this->moveFile($file, "mask", $name);
            $this->createPreview($file, "mask", $name);
            return $name;
        }
        else
        {
            throw "Format not supported";
        } 
    }

    /* --------------------------------------------------------------------
     * Úprava dat masky (změna záznamu masky)
     * -------------------------------------------------------------------- */
    
    
    /* Předá datové vrstvě informace potřebné pro změnu záznamu */
    private function modifyMaskData($values, $mask)
    {
        $mask->setName($values['name']);
        if ($values['privacy']) {
            $mask->makePublic();
        } else {
            $mask->makePrivate();
        }

        if ($values['usage'] == 'universal') {
            $mask->makeUniversal();
        } else {
            $mask->makeCustom();
        }
        $mask->setNote($values['note']);
    }


    /* --------------------------------------------------------------------
     * PUBLIC Přidání materiálu (form i zpracování)
     * -------------------------------------------------------------------- */


    /* Provede přidání masky */
    public function addMask(Form $form, $values)
    {
        if ($values["file"] == NULL or !$values["file"]->isOk())
        {
            throw "Image upload failed!";
        }
        
        $fileName = $this->uploadMask($values["file"]);         
        $mask = $this->createMask($values, $fileName);
        
        $this->setMaskContext($values, $mask);
        $this->maskFacade->itemEdited();
        $form->getPresenter()->redirect('Mask:detail', array("maskId" => $mask->getId()));
    }

    /* Vytvoří formulář pro nahrání masky */
    public function addMaskForm()
    {
		$form = $this->maskForm->create();
		$form->onSuccess[] = array($this, 'addMask');
		return $form;
    }


    /* --------------------------------------------------------------------
     * PUBLIC Úprava masky (form i zpracování)
     * -------------------------------------------------------------------- */


    /* Upraví informace o masce */
    public function modifyMask(Form $form, $values)
    {
    	$maskId = $form->getPresenter()->getParameter('maskId');
    	$mask = $this->maskFacade->findById($maskId);
        $this->modifyMaskData($values, $mask);
        $this->setMaskContext($values, $mask);
    	$this->maskFacade->itemEdited();
    	$form->getPresenter()->redirect('Mask:detail', array("maskId" => $mask->getId()));
    }

    /* Vytvoří formulář pro úpravu informací o masce */
    public function modifyMaskForm($maskId)
    {
    	$mask = $this->maskFacade->findById($maskId);
        if (!$mask)
        {
            throw new BadRequestException;
        }
        $materials = $this->materialFacade->findAllByMask($maskId);
    	$form = $this->maskForm->modify($mask, $materials);
    	$form->onSuccess[] = array($this, 'modifyMask');
    	return $form;
    }

    /* Pokud není přihlášený uživatel vlastník masky, funkce vyhodí výjimku */
    public function checkPermission($maskId)
    {
        $mask = $this->maskFacade->findById($maskId);
        if (!$mask)
        {
            throw new BadRequestException;
        }
        if (!$this->checkSession($mask->author))
        {
            throw 'Nedostatečná práva';
        }

    }


    /* --------------------------------------------------------------------
     * PUBLIC Nastaví tabulky
     * -------------------------------------------------------------------- */


    /* Nastaví text atributů itemu */
    public function setupAttributes($item)
    {
        if ($item->public)
        {
            $item->public = 'ano';
        } 
        else
        {
            $item->public = 'ne';
        }
        if ($item->universal)
        {
			$item->universal = 'ano';
	    }
	    else
	    {
			$item->universal = 'ne';
	    }
        $item->size = $this->filesizeFormatted($item->size);
        $item->dateUpload = $item->dateUpload->format('d.m.Y H:i');
        $item->author = $this->userFacade->findById($item->authorId)->getName();
    }

    /* Přiřadí id a cestu náhledu itemu */
    public function addPreview($item, $sourceItem, $preview)
    {
        $item->previewId = $sourceItem->preview;
        $item->previewPath = $preview->getPath();
    }

    /* Nastaví item */
    public function setupGridItem($item, $sourceItem, $preview)
    {
        $this->addPreview($item, $sourceItem, $preview);
        $this->setupAttributes($item);
    }


    /* --------------------------------------------------------------------
     * PUBLIC PrepareData funkce (ByUser, ByMask...)
     * -------------------------------------------------------------------- */


    /*  Vyplní grid podle autora */
    public function prepareDataAllByUser($order, Paginator $paginator = NULL)
    {
		$user = $this->user->id;
		$selection = $this->maskFacade->findAllByAuthor($user, $order, $paginator);
		$gridSelection = [];
		foreach ($selection as $item)
		{
		    $gridItem = new AT\MaskItem($item);
		    $preview = $this->previewFacade->findById($item->preview);
		    $this->setupGridItem($gridItem, $item, $preview);
		    $gridSelection[] = $gridItem;
		}
		return $gridSelection;
    }

    /*  Vyplní grid podle materiálu */
	public function prepareDataAllByMaterial($order, Paginator $paginator = NULL)
	{
		$selection = $this->maskFacade->findAllByMaterial($this->materialId, $order, $paginator);
		$gridSelection = [];
		foreach ($selection as $item)
		{
			/*if (!$item->public && ($item->author != $this->user->id))
			{
				continue;
			}*/
			$gridItem = new AT\MaskItem($item);
    	    $preview = $this->previewFacade->findById($item->preview);
            $this->setupGridItem($gridItem, $item, $preview);
			$gridSelection[] = $gridItem;
		}

		return $gridSelection;
    }

    public function getDataAllByUser($filter, $order, Paginator $paginator = NULL)
    {
		$selection = $this->prepareDataAllByUser($order, $paginator);
		return $selection;
    }

    public function getDataAllByMaterial($filter, $order, Paginator $paginator = NULL)
    {
		$selection = $this->prepareDataAllByMaterial($order, $paginator);
		return $selection;
    }

    public function getDataSourceSumUser($filter, $order)
    {
		$selection = $this->maskFacade->findAllByAuthor($this->user->id);
		return $this->countVisible($selection, $this->user->id);
    }

    public function getDataSourceSumMaterial($filter, $order)
    {
    	$selection = $this->maskFacade->findAllByMaterial($this->materialId);
        return $this->countVisible($selection, $this->user->id);
    }

    public function tableByUser()
    {
		$grid = $this->maskTable->createByUser();
		$grid->setDataSourceCallback([$this, 'getDataAllByUser']);
		$grid->setPagination(4, [$this, 'getDataSourceSumUser']);
		return $grid;
    }

    public function tableByMaterial($materialId)
    {
		$grid = $this->maskTable->createByMaterial();
		$this->materialId = $materialId;
		$grid->setDataSourceCallback([$this, 'getDataAllByMaterial']);
		$grid->setPagination(4, [$this, 'getDataSourceSumMaterial']);
		return $grid;
    }

    public function delete($maskId){    
        return $this->maskFacade->delete($maskId);
    }

}
