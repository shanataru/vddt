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

class Search extends AM {

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

    /** @var AT\MaterialTable */
    private $materialTable;

    
    /* --------------------------------------------------------------------
     * PUBLIC Konstruktor
     * -------------------------------------------------------------------- */
    

    public function __construct(AD\Materials $materials, AD\Previews $previews, AD\Processings $processings, AD\Masks $masks, AD\CameraMoves $cameraMoves, AD\CameraTakes $cameraTakes, AD\Tags $tags, AD\Users $users, AT\MaterialTable $materialTable, User $user)
    {
        parent::__construct($user);
        $this->materialFacade = $materials;
        $this->processingFacade = $processings;
        $this->previewFacade = $previews;
        $this->maskFacade = $masks;
        $this->userFacade = $users;
        $this->cameraMoveFacade = $cameraMoves;
        $this->cameraTakeFacade = $cameraTakes;
        $this->materialTable = $materialTable;
        $this->tagFacade = $tags;
        $this->user = $user;
    }

    /* --------------------------------------------------------------------
     * PUBLIC Vyhledávání
     * -------------------------------------------------------------------- */


    /* Dostane jako argument string zadany uzivatelem, vrati pole s objekty DAO
     * -- TIP ale mozna to bude potreba udelat jinak --
     * Vracene pole by mohlo byt asociativni s polozkami 
     * "materials", "processings", "masks", "binaries", tedy neco jako
     * data["materials"] = [ material1, material2 ... ]
     * data["masks"] = [ mask1, mask2 ... ]
     *
	 * Prohledava materialy, masky, zpracovani a binarky
     */
    public function searchAll($query)//nevim jestli by se ta query nemela tvorit az tady v modelu misto v presenteru ???
    {

    }

    /* Prohleda pouze materialy, parametr je opet string query */
    public function searchMaterials($query)
    {

    }

    /* Prohleda pouze masky, parametr je opet string query */
    public function searchMasks($query)
    {

    }

    /* Prohleda pouze binarky, parametr je opet string query */
    public function searchBinaries($query)
    {

    }

	/* Prohleda pouze ypracovani, parametr je opet string query */
    public function searchProcessings($query)
    {

    }


    /* QUERY MAGIE*/
    public  function searchFor($params){
        //tady rozparsovane kousky budem hazet uz specializovanym fcim (viz vyse) (na uzivatele etc)
        //milion ifu podle toho co hledame a pak neco takovehoto:
        //todo
        // a toto bude v nejake z tech hornich fci: $results = $this->userFacade->findByQuery($params); volame ve prisluslne fasade prislusny QueryObject ()
        //return $results; //
    }

    /*tady budeme parsovat textik od uzivatele a predavat do searchFor kde se bude tvorit cele filtrovani*/
    public function parseUserInput($form, $values){
        //$params = todo
        //return $this->searchFor($params);
    }

    public function searchForm()
    {
        //$form = $this->searchForm->create();//todo
        //$form->onSuccess[] = array($this, 'parseUserInput');
        //return $form;
    }




}