<?php

namespace App\Model;

use Nette;
use Nette\Security\User;
use App\Data as AD;
use App\Tables as AT;
use App\Model\Model as AM;
use Nette\Utils\Paginator;

class Processing extends AM {

    /** @var AD\Processings */
    private $processingFacade;

    /** @var AD\ProcessingTable */
    private $processingTable;

    /** @var AD\Materials */
    private $materialsFacade;

    /** @var AD\Masks */
    private $maskFacade;

    /** @var AD\Binaries */
    private $binariesFacade;

    private $materialId;
    private $maskId;
    private $binaryId;

    public function __construct(AD\Processings $processings, AT\ProcessingTable $processingsTable, AD\Binaries $binaries, AD\Materials $materials, AD\Masks $masks, AD\Users $users, User $user) {
    	parent::__construct($user);
	$this->processingFacade = $processings;
	$this->binariesFacade = $binaries;
	$this->materialsFacade = $materials;
	$this->maskFacade = $masks;
	$this->userFacade = $users;
	$this->processingTable = $processingsTable;
	$this->user = $user;
    }

    public function prepareDataAll($order, Paginator $paginator = NULL) {
	$user = $this->user->id;

	$selection = $this->processingFacade->findAllByAuthor($user, $order, $paginator);
	$gridSelection = [];
	foreach ($selection as $item) {
	    $gridItem = new AT\ProcessingItem($item);

	    $mask = $this->maskFacade->findById($item->mask);
	    $material = $this->materialsFacade->findById($item->material);
	    $binary = $this->binariesFacade->findById($item->binary);

	    if ($item->mask) {
		$gridItem->mask = $mask->getName();
		$gridItem->maskId = $item->mask;
	    }

	    if ($item->material) {
		$gridItem->material = $material->getName();
		$gridItem->materialId = $item->material;
	    }

	    if ($item->binary) {
		$gridItem->binary = $binary->getName();
		$gridItem->binaryId = $item->binary;
	    }

	    //--------public state------------
	    if ($gridItem->public) {
		$gridItem->public = 'ano';
	    } else {
		$gridItem->public = 'ne';
	    }
	    //-------- processing status---------
	    $gridItem->statusNo = $gridItem->status;
	    if ($gridItem->status == 1) {
		$gridItem->status = 'pracuje';
	    } else if ($gridItem->status == 2) {
		$gridItem->status = 'čeká na potvrzení';
	    } else {
		$gridItem->status = 'hotovo';
	    }

	    $gridSelection[] = $gridItem;
	}

	return $gridSelection;
    }

    public function prepareDataAllByMaterial($order, Paginator $paginator = NULL) {
	$selection = $this->processingFacade->findAllByMaterial($this->materialId, $order, $paginator);
	$gridSelection = [];
	foreach ($selection as $item) {
	    $gridItem = new AT\ProcessingItem($item);
	    $gridItem->material = $item->name;
	    $gridItem->materialId = $item->id;
	    $autorId = $item->author;
	    $gridItem->author = $this->userFacade->findById($item->author)->getName();
	    $mask = $this->maskFacade->findById($item->mask);
	    $binary = $this->binariesFacade->findById($item->binary);

	    if ($item->mask) {
		$gridItem->mask = $mask->getName();
		$gridItem->maskId = $item->mask;
	    }

	    if ($item->binary) {
		$gridItem->binary = $binary->getName();
		$gridItem->binaryId = $item->binary;
	    }

	    //--------public state------------
	    if ($gridItem->public) {
		$gridItem->public = 'ano';
	    } else {
		$gridItem->public = 'ne';
	    }
	    //-------- processing status---------
	    $gridItem->statusNo = $gridItem->status;
	    if ($gridItem->status == 1) {
		$gridItem->status = 'pracuje';
	    } else if ($gridItem->status == 2) {
		$gridItem->status = 'čeká na potvrzení';
	    } else {
		$gridItem->status = 'hotovo';
	    }

	    if ($gridItem->public == 'ano' || $autorId == $this->user->id) {
		  $gridSelection[] = $gridItem;
	    }
	}

	return $gridSelection;
    }

    public function prepareDataAllByMask($order, Paginator $paginator = NULL) {
	$selection = $this->processingFacade->findAllByMask($this->maskId, $order, $paginator);
	$gridSelection = [];
	foreach ($selection as $item) {
	    $gridItem = new AT\ProcessingItem($item);
	    $autorId = $item->author;
	    $gridItem->author = $this->userFacade->findById($item->author)->getName();
	    $material = $this->materialsFacade->findById($item->material);
	    $binary = $this->binariesFacade->findById($item->binary);

	    if ($item->material) {
		$gridItem->material = $material->getName();
		$gridItem->materialId = $item->material;
	    }

	    if ($item->binary) {
		$gridItem->binary = $binary->getName();
		$gridItem->binaryId = $item->binary;
	    }

	    //--------public state------------
	    if ($gridItem->public) {
		$gridItem->public = 'ano';
	    } else {
		$gridItem->public = 'ne';
	    }
	    //-------- processing status---------
	    $gridItem->statusNo = $gridItem->status;
	    if ($gridItem->status == 1) {
		$gridItem->status = 'pracuje';
	    } else if ($gridItem->status == 2) {
		$gridItem->status = 'čeká na potvrzení';
	    } else {
		$gridItem->status = 'hotovo';
	    }

	    if ($gridItem->public == 'ano' || $autorId == $this->user->id) {
		$gridSelection[] = $gridItem;
	    }
	}

	return $gridSelection;
    }

    public function prepareDataAllByBinary($order, Paginator $paginator = NULL) {
	$selection = $this->processingFacade->findAllByBInary($this->binaryId, $order, $paginator);
	$gridSelection = [];
	foreach ($selection as $item) {
	    $gridItem = new AT\ProcessingItem($item);
	    $autorId = $item->author;
	    $gridItem->author = $this->userFacade->findById($item->author)->getName();
	    $material = $this->materialsFacade->findById($item->material);
	    $mask = $this->maskFacade->findById($item->mask);

	    if ($item->material) {
		$gridItem->material = $material->getName();
		$gridItem->materialId = $item->material;
	    }

	    if ($item->mask) {
		$gridItem->mask = $mask->getName();
		$gridItem->maskId = $item->mask;
	    }

	    //--------public state------------
	    if ($gridItem->public) {
		$gridItem->public = 'ano';
	    } else {
		$gridItem->public = 'ne';
	    }
	    //-------- processing status---------
	    $gridItem->statusNo = $gridItem->status;
	    if ($gridItem->status == 1) {
		$gridItem->status = 'pracuje';
	    } else if ($gridItem->status == 2) {
		$gridItem->status = 'čeká na potvrzení';
	    } else {
		$gridItem->status = 'hotovo';
	    }

	    if ($gridItem->status == 'hotovo' && ($gridItem->public == 'ano' || $autorId == $this->user->id)) {
		$gridSelection[] = $gridItem;
	    }
	}

	return $gridSelection;
    }

    public function getDataAll($filter, $order, Paginator $paginator = NULL) {
	$selection = $this->prepareDataAll($order, $paginator);
	return $selection;
    }

    public function getDataSourceSumAuthor($filter, $order) {
	$count = $this->processingFacade->countByAuthorId($this->user->id);
	return $count;
    }
    
    public function getDataSourceSumMask($filter, $order)
    {
    	$selection = $this->processingFacade->findAllByMask($this->maskId);
    	return $this->countVisible($selection, $this->user->id);
    }
    
    public function getDataSourceSumMaterial($filter, $order)
    {
        $selection = $this->processingFacade->findAllByMaterial($this->materialId);
        return $this->countVisible($selection, $this->user->id);
    }
    
        public function getDataSourceSumBinary($filter, $order) {
	$count = $this->processingFacade->countByBinaryId($this->binaryId);
	return $count;
    }

    public function getDataAllByMaterial($filter, $order, Paginator $paginator = NULL) {
	$selection = $this->prepareDataAllByMaterial($order, $paginator);
	return $selection;
    }

    public function getDataAllByMask($filter, $order, Paginator $paginator = NULL) {
	$selection = $this->prepareDataAllByMask($order, $paginator);
	return $selection;
    }

    public function getDataAllByBinary($filter, $order, Paginator $paginator = NULL) {
	$selection = $this->prepareDataAllByBinary($order, $paginator);
	return $selection;
    }

    public function tableByUser() {
	$grid = $this->processingTable->createByUser();
	//LOOK HERE - K vyrobenému gridu navážu funkci která si to bude skrz fasádu tahat ty informace...
	$grid->setDataSourceCallback([$this, 'getDataAll']);
	$grid->setPagination(4, [$this, 'getDataSourceSumAuthor']);
	return $grid;
    }

    public function tableByMaterial($materialId) {
	$grid = $this->processingTable->createByMaterial();
	$this->materialId = $materialId;
	$grid->setDataSourceCallback([$this, 'getDataAllByMaterial']);
	$grid->setPagination(4, [$this, 'getDataSourceSumMaterial']);
	return $grid;
    }

    public function tableByMask($maskId) {
	$grid = $this->processingTable->createByMask();
	$this->maskId = $maskId;
	$grid->setDataSourceCallback([$this, 'getDataAllByMask']);
	$grid->setPagination(4, [$this, 'getDataSourceSumMask']);
	return $grid;
    }

    public function tableByBinary($binaryId) {
	$grid = $this->processingTable->createByBinary();
	$this->binaryId = $binaryId;
	$grid->setDataSourceCallback([$this, 'getDataAllByBinary']);
	$grid->setPagination(4, [$this, 'getDataSourceSumBinary']);
	return $grid;
    }

}
