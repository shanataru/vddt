<?php

namespace App\Model;

use Nette;
use Nette\Security\User;
use App\Forms as FO;
use App\Data as AD;
use App\Tables as AT;
use App\Model\Model as AM;
use Nette\Application\UI\Form;
use Nette\Utils\Image;
use Nette\Utils\Paginator;

class Material extends AM {

    /** @var FO\MaterialForm */
    private $materialForm;

    /** @var AD\Materials */
    private $materialFacade;

    /** @var AD\Previews */
    private $previewFacade;

    /** @var AD\Processings */
    private $processingFacade;

    /** @var AD\Masks */
    private $maskFacade;

    /** @var AD\Users */
    private $userFacade;

    /** @var AD\CameraMoves */
    private $cameraMoveFacade;

    /** @var AD\CameraTakes */
    private $cameraTakeFacade;

    /** @var AD\Tags */
    private $tagFacade;

    /** @var AD\MaskTextures */
    private $maskTextureFacade;

    /** @var AT\MaterialTable */
    private $materialTable;

    private $maskId;

    
    /* --------------------------------------------------------------------
     * PUBLIC Konstruktor
     * -------------------------------------------------------------------- */
    

    public function __construct(FO\MaterialForm $materialForm, AD\Materials $materials, AD\Previews $previews, AD\Processings $processings, AD\Masks $masks, AD\CameraMoves $cameraMoves, AD\CameraTakes $cameraTakes, AD\Tags $tags, AD\MaskTextures $maskTextures, AD\Users $users, AT\MaterialTable $materialTable, User $user)
    {
        parent::__construct($user);
        $this->materialForm = $materialForm;
        $this->materialFacade = $materials;
        $this->processingFacade = $processings;
        $this->previewFacade = $previews;
        $this->maskFacade = $masks;
        $this->userFacade = $users;
        $this->cameraMoveFacade = $cameraMoves;
        $this->cameraTakeFacade = $cameraTakes;
        $this->materialTable = $materialTable;
        $this->tagFacade = $tags;
        $this->maskTextureFacade = $maskTextures;
        $this->user = $user;
    }

    
    /* --------------------------------------------------------------------
     * PUBLIC Basic metoda pro získání informací o materiálu
     * -------------------------------------------------------------------- */
    
    
    public function getData($materialId)
    {
        $data = [];
        $material = $this->materialFacade->findById($materialId);
        $data["previewPath"] = $this->previewFacade->findById($material->getPreviewId())->getPath();
        $move = $this->cameraMoveFacade->findById($material->getCameraMoveId());
        $data["move"] = $move ? $move->getMove() : "-";
        $take = $this->cameraTakeFacade->findById($material->getCameraTakeId());
        $data["take"] = $take ? $take->getTake() : "-";
        $data["author"] = $this->userFacade->findById($material->getAuthorId())->getName();
        $data["filesize"] = $this->filesizeFormatted($material->size);
        
        $tags = $this->tagFacade->findByTexture($material->id);
        $data["tags"] = [];    
        
        foreach ($tags as $tag)
        {
            $data["tags"][] = $tag->name;
        }

        $selection = $this->processingFacade->findAllByMaterial($materialId);
        $data["countProcessing"] = $this->countVisible($selection, $this->user->id);
        $selection = $this->maskFacade->findAllByMaterial($materialId);
        $data["countMask"] = $this->countVisible($selection, $this->user->id);
        $data["filename"] = $material->getName();
        $data["filepath"] = $material->getPath();
        $data["dimensions"] = $material->getWidth() . "×" . $material->getHeight();
        $data["format"] =  $this->getFormat();
        $data["privacy"] =  ($material->isPublic()) ? "veřejné" : "soukromé";
        $data["mediaType"] =  $this->getMediaType();
        $data["uploadTime"] =  $material->uploadedAt()->format('d.m.Y H:i');
        $data["note"] = $material->getNote();
        $data["owner"] = $this->checkSession($material->author);
        return $data;
    }    
    
    
    /* --------------------------------------------------------------------
     * Zpracování souborů při uploadu materiálu
     * -------------------------------------------------------------------- */

      
    /* Provede upload souborů a vrátí POUZE jméno souboru ve struktuře (ne cestu) na ftp serveru */
    private function uploadMaterial($file)
    {
        if ($file->isImage())
        {
            $format = $this->getFormat($file);
            $name = $this->createName($format);
            $this->moveFile($file, "material", $name);
            $this->createPreview($file, "material", $name);
            return $name;
        }
    }
    
    
    /* --------------------------------------------------------------------
     * Přidání kontextu materiálu (tagy, záběr a pohyb kamery...)
     * -------------------------------------------------------------------- */
    
    
    /* Najde/vytvoří druh záběru */
    private function getTake($take)
    {
       $takeObject = $this->cameraTakeFacade->findByTake($take);
	    if ($takeObject == NULL)
        {
		  $takeObject = $this->cameraTakeFacade->makeNew($take);
	    }
        return $takeObject;
    }
    
    /* Najde/vytvoří pohyb kamery */
    private function getMove($move)
    {
       $moveObject = $this->cameraMoveFacade->findByMove($move);
	    if ($moveObject == NULL)
        {
		  $moveObject = $this->cameraMoveFacade->makeNew($move);
	    }
        return $moveObject;
    }
    
    /* Přidá kontext materiálu */
    private function setMaterialContext($context, $material)
    {
        $material->setCameraTakeId($context["take"]->id);
        $material->setCameraMoveId($context["move"]->id);
    }
    
    /* Vytvoří kontext materiálu */
    private function getMaterialContext($values)
    {
        $context = [];
        $context["take"] = $this->getTake($values["take"]);
        $context["move"] = $this->getMove($values["move"]);
        return $context;
    }
    
    /* ze stringu naprasuje tagy (zbaví se duplicit a ožeže whitespace), oddělovač je čárka */
    private function parseTags($tags)
    {
        $tags_array = array_filter(explode(',', $tags), 'strlen');
        $tags_trimmed = array_map('trim', $tags_array);
        $tags_clean_array = array_unique($tags_trimmed);
        return $tags_clean_array;
    }
    
    /* Vrátí tag DAO, pokud neexistuje, tak ho vytvoří */
    private function getTagObject($tag){
        $tagObject = $this->tagFacade->findByName($tag);
        if (!$tagObject)
        {
            $tagObject = $this->tagFacade->makeNew($tag);
        }
        return $tagObject;
    }
    
    /* Vrátí pole tagů z tagsA, které nejsou mezi tagsB */
    private function filterTags($tagsA, $tagsB)
    {
        $aTagsId = [];
        $bTagsId = [];
        $output = [];
        
        foreach($tagsB as $tag)
        {
            $bTagsId[] = $tag->id;
        }
        foreach($tagsA as $tag)
        {
            $aTagsId[] = $tag->id;
        }
        
        $aTagsId = array_diff($aTagsId, $bTagsId);
        
        foreach($tagsA as $tag)
        {
            if (in_array($tag->id, $aTagsId))
            {
                $output[] = $tag;
            }
                
        }
        return $output;    
    }
    
    /* Přidá všechny tagy z vloženého pole k danému materiálu */
    private function addNewTags($tags, $material)
    {
        foreach($tags as $tag)
        {
            $this->tagFacade->addTagToTexture($tag->id, $material->id);
        }
    }
    
    /* Odebere tagy danému materiálu */
    private function removeOldTags($tags, $material)
    {
        foreach($tags as $tag)
        {
            $this->tagFacade->removeTagFromTexture($tag->id, $material->id);
        }
    }  
    
    /* Přidá všechny nové tagy a vrátí pole neplatných tagů */
    private function addTags($tags, $material)
    {
        $oldTags = $this->tagFacade->findByTexture($material->id);
        $newTags = [];   
        foreach($tags as $tag)
        {
            $newTags[] = $this->getTagObject($tag);
        }
        $filtredTags = $this->filterTags($newTags, $oldTags);
        $this->addNewTags($filtredTags, $material);
        return $this->filterTags($oldTags, $newTags);     
    }
    
    /* updatne tagy u materiálu podle vstupu */
    private function setMaterialTags($values, $material)
    {
        $tags = $values["tags"];
        $tags = $this->parseTags($tags);
        $unused_tags = $this->addTags($tags, $material);
        $this->removeOldTags($unused_tags, $material);
    }
    
    
    /* --------------------------------------------------------------------
     * Vytvoření materiálu (nahrání textury a náhledu)
     * -------------------------------------------------------------------- */
     
    
    /* Předá datové vrstvě potřebná data pro vytvoření záznamu materiálu a náhledu, nahraje náhled */
    private function createMaterial($values, $fileName)
    {     
	    $preview = $this->previewFacade->makeNew(parent::PREVIEWPATHSHORT . $fileName, NULL);
        $originalPath = parent::MATERIALPATH . $fileName;
        $path = parent::MATERIALPATHSHORT . $fileName;
        $fileSize = filesize($originalPath);
        $width; $height;

        if ($values["file"]->isImage())
        {
            $image = Image::fromFile($originalPath);
            $width = $image->width;
            $height = $image->height;
        }

        $extension = $this->getFormat($values["file"]);
        $user = $this->user->id;
        
	    $material = $this->materialFacade->makeNew($values["name"], $height, $width, $extension, $fileSize, $user, $preview->id, $path, NULL, NULL, NULL, NULL, $values["privacy"], $values["note"]);
        return $material;
    }
    
    
    /* --------------------------------------------------------------------
     * Úprava materiálu (nahrání textury)
     * -------------------------------------------------------------------- */
    
    
    /* Předá datové vrstvě informace potřebné pro změnu záznamu */
    private function modifyMaterialData($values, $material)
    {
        $material->setName($values['name']);
        $this->setMaterialTags($values, $material);
        if ($values['privacy']) {
            $material->makePublic();
        } else {
            $material->makePrivate();
        }
        $material->setNote($values['note']);
    }
    
    
    /* --------------------------------------------------------------------
     * PUBLIC Přidání materiálu (form i zpracování)
     * -------------------------------------------------------------------- */
     
      
    /* Provede přidání materiálu */
    public function addMaterial(Form $form, $values)
    {
        if ($values["file"] == NULL or !$values["file"]->isOk())
        {
            throw "Image upload failed!";
        }
        
        $fileName = $this->uploadMaterial($values["file"]);         
        $context = $this->getMaterialContext($values);
        $material = $this->createMaterial($values, $fileName);
        
        $this->setMaterialContext($context, $material);
        $this->setMaterialTags($values, $material);
        $this->materialFacade->itemEdited();
        $form->getPresenter()->redirect('Material:detail', array("materialId" => $material->getId()));
    }
    
    /* Vytvoří komponentu formulář nahrávání materiálu */
    public function addMaterialForm()
    {
	   $form = $this->materialForm->create();
	   $form->onSuccess[] = array($this, 'addMaterial');
	   return $form;
    }
    
    
    /* --------------------------------------------------------------------
     * PUBLIC Úprava materiálu (form i zpracování)
     * -------------------------------------------------------------------- */
    
    
    /* Upraví informace materiálu */
    public function modifyMaterial(Form $form, $values)
    {
        $materialId = $form->getPresenter()->getParameter('materialId');
        $material = $this->materialFacade->findById($materialId);
        $context = $this->getMaterialContext($values);
        
        $this->modifyMaterialData($values, $material);
        $this->setMaterialContext($context, $material);
        $this->materialFacade->itemEdited();
        $form->getPresenter()->redirect('Material:detail', array("materialId" => $materialId));
    }

    /* Vytvoří komponentu formulář úpravy materiálu */
    public function modifyMaterialForm($materialId)
    {
        $material = $this->materialFacade->findById($materialId);
        if (!$material)
        {
            throw new BadRequestException;
        }
        $take = $this->cameraTakeFacade->findById($material->getCameraTakeId());
        $move = $this->cameraMoveFacade->findById($material->getCameraMoveId());
        $tags = $this->tagFacade->findByTexture($materialId);
        $tagsText = [];
        foreach ($tags as $tag)
        {
            $tagsText[] = $tag->name;
        }
        $form = $this->materialForm->modify($material, $take, $move, $tagsText);
        $form->onSuccess[] = array($this, 'modifyMaterial');
        return $form;
    }

    /* Pokud není přihlášený uživatel vlastník materiálu, funkce vyhodí výjimku */
    public function checkPermission($materialId)
    {
        $material = $this->materialFacade->findById($materialId);
        if (!$material)
        {
            throw new BadRequestException;
        }
        if (!$this->checkSession($material->author))
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


    /* Vyplní grid podle autora */
    public function prepareDataAllByUser($order, Paginator $paginator = NULL)
    {
        $user = $this->user->id;
    	$selection = $this->materialFacade->findAllByAuthor($user, $order, $paginator);
    	$gridSelection = [];
    	foreach ($selection as $item)
        {
    	    $gridItem = new AT\MaterialItem($item);
    	    $preview = $this->previewFacade->findById($item->preview);
            $this->setupGridItem($gridItem, $item, $preview);
    	    $gridSelection[] = $gridItem;
    	}
    	return $gridSelection;
    }

    /* Vyplní grid podle masky */
    public function prepareDataAllByMask($order, Paginator $paginator = NULL)
    {
        $selection = $this->materialFacade->findAllByMask($this->maskId, $order, $paginator);
    	$gridSelection = [];
    	
        foreach ($selection as $item) {
            if (!$item->public && ($item->author != $this->user->id))
            {
                continue;
            }
    	    $gridItem = new AT\MaterialItem($item);
    	    $preview = $this->previewFacade->findById($gridItem->previewId);
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

    public function getDataAllByMask($filter, $order, Paginator $paginator = NULL)
    {
	   $selection = $this->prepareDataAllByMask($order, $paginator);
	   return $selection;
    }

    public function getDataSourceSumUser($filter, $order)
    {
        $selection = $this->materialFacade->findAllByAuthor($this->user->id);
        return $this->countVisible($selection, $this->user->id);
    }

    public function getDataSourceSumMask($filter, $order)
    {
        $selection = $this->materialFacade->findAllByMask($this->maskId);
        return $this->countVisible($selection, $this->user->id);
    }

    public function tableByUser($username)
    {
	   $grid = $this->materialTable->createByUser();
	   $grid->setDataSourceCallback([$this, 'getDataAllByUser']);
	   $grid->setPagination(4, [$this, 'getDataSourceSumUser']);
	   return $grid;
    }

    public function tableByMask($maskId) {
		$grid = $this->materialTable->createByMask();
		$this->maskId = $maskId;
		$grid->setDataSourceCallback([$this, 'getDataAllByMask']);
		$grid->setPagination(4, [$this, 'getDataSourceSumMask']);
		return $grid;
    }
	
	public function delete($materialId){	
		return $this->materialFacade->delete($materialId);
	}
	

}
