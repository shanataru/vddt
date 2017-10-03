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

class Homepage extends AM {

    /** @var AD\Processings */
    private $maskFacade;

    /** @var AD\Materials */
    private $materialFacade;

    /** @var AD\Binaries */
    private $binaryFacade;

    /** @var AD\Processings */
    private $processingFacade;

    public function __construct(AD\Masks $masks, AD\Binaries $binaries, AD\Materials $materials, AD\Processings $processings) {
	$this->maskFacade = $masks;
	$this->materialFacade = $materials;
	$this->processingFacade = $processings;
	$this->binaryFacade = $binaries;
    }

    public function getData() {

	return array("countProcessing" => $this->processingFacade->countAll(),
	    "countBinaries" => $this->binaryFacade->countAll(),
	    "countMasks" => $this->maskFacade->countAll(),
	    "countMaterials" => $this->materialFacade->countAll()
	);
    }

}
