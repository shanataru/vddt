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

    /* --------------------------------------------------------------------
    * PUBLIC Konstruktor
    * -------------------------------------------------------------------- */

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


    /* --------------------------------------------------------------------
    * PUBLIC Tabulky - pomocne fce
    * -------------------------------------------------------------------- */


    public function addMask($item, $gridItem, $mask = NULL)
    {
    	if (!$item->mask)
    		return;

    	if ($mask == NULL)
    	{
    		$mask = $this->maskFacade->findById($item->mask);
    	}

    	$gridItem->mask = $mask->name;
		$gridItem->maskId = $mask->id;
    }

    public function addMaterial($item, $gridItem, $material = NULL)
    {
    	if (!$item->material)
    		return;

    	if ($material == NULL)
    	{
    		$material = $this->materialsFacade->findById($item->material);
    	}

    	$gridItem->material = $material->name;
		$gridItem->materialId = $material->id;
    }


	public function addBinary($item, $gridItem, $binary = NULL)
    {
    	if (!$item->binary)
    		return;

    	if ($binary == NULL)
    	{
    		$binary = $this->binariesFacade->findById($item->binary);
    	}

    	$gridItem->binary = $binary->name;
		$gridItem->binaryId = $binary->id;
    }

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
		  
		$item->statusNo = $item->status;
		
		switch ($item->status)
		{
		    case 1:
		        $item->status = 'pracuje';
		        break;
		    case 2:
		        $item->status = 'čeká na potvrzení';
		        break;
		    default:
		        $item->status = 'hotovo';
		        break;
		}
    }


	/* --------------------------------------------------------------------
    * PUBLIC Tabulky 
    * -------------------------------------------------------------------- */


    public function prepareDataAllByAuthor($order, Paginator $paginator = NULL)
    {
		$user = $this->user->id;
		$selection = $this->processingFacade->findAllByAuthor($user, $order, $paginator);
		$gridSelection = [];

		foreach ($selection as $item)
		{
		    $gridItem = new AT\ProcessingItem($item);
		    $this->addMask($item, $gridItem);
		    $this->addMaterial($item, $gridItem);
		    $this->addBinary($item, $gridItem);
		    $this->setupAttributes($gridItem);
		    $gridSelection[] = $gridItem;
		}

		return $gridSelection;
    }

    public function prepareDataAllByMaterial($order, Paginator $paginator = NULL)
    {
		$selection = $this->processingFacade->findAllByMaterial($this->materialId, $order, $paginator);
		$gridSelection = [];

		foreach ($selection as $item)
		{
			if (!$item->public && ($item->author != $this->user->id))
            {
                continue;
            }

		    $gridItem = new AT\ProcessingItem($item);
		    $this->addMask($item, $gridItem);
		    $this->addMaterial($item, $gridItem);
		    $this->addBinary($item, $gridItem);
		    $this->setupAttributes($gridItem);
		    $gridItem->author = $this->userFacade->findById($item->author)->getName();
			$gridSelection[] = $gridItem;
		}

		return $gridSelection;
    }

    public function prepareDataAllByMask($order, Paginator $paginator = NULL)
    {
		$selection = $this->processingFacade->findAllByMask($this->maskId, $order, $paginator);
		$gridSelection = [];
		foreach ($selection as $item)
		{
		    if (!$item->public && ($item->author != $this->user->id))
            {
                continue;
            }

		    $gridItem = new AT\ProcessingItem($item);
		    $this->addMask($item, $gridItem);
		    $this->addMaterial($item, $gridItem);
		    $this->addBinary($item, $gridItem);
		    $this->setupAttributes($gridItem);
		    $gridItem->author = $this->userFacade->findById($item->author)->getName();
			$gridSelection[] = $gridItem;
		}

		return $gridSelection;
    }

    public function prepareDataAllByBinary($order, Paginator $paginator = NULL)
    {
		$selection = $this->processingFacade->findAllByBinary($this->binaryId, $order, $paginator);
		$gridSelection = [];
		foreach ($selection as $item)
		{
		    if (!$item->public && ($item->author != $this->user->id))
            {
                continue;
            }

		    $gridItem = new AT\ProcessingItem($item);
		    $this->addMask($item, $gridItem);
		    $this->addMaterial($item, $gridItem);
		    $this->addBinary($item, $gridItem);
		    $this->setupAttributes($gridItem);
		    $gridItem->author = $this->userFacade->findById($item->author)->getName();
			$gridSelection[] = $gridItem;
		    
		}

		return $gridSelection;
    }

    public function getDataSourceSumAuthor($filter, $order)
    {
		$selection = $this->processingFacade->findAllByAuthor($this->user->id);
		return $this->countVisible($selection, $this->user->id);
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
    
    public function getDataSourceSumBinary($filter, $order)
    {
		$selection = $this->processingFacade->findAllByBinary($this->binaryId);
		return $this->countVisible($selection, $this->user->id);
    }

	public function getDataAllByAuthor($filter, $order, Paginator $paginator = NULL)
    {
		$selection = $this->prepareDataAllByAuthor($order, $paginator);
		return $selection;
    }

    public function getDataAllByMaterial($filter, $order, Paginator $paginator = NULL)
    {
		$selection = $this->prepareDataAllByMaterial($order, $paginator);
		return $selection;
    }

    public function getDataAllByMask($filter, $order, Paginator $paginator = NULL)
    {
		$selection = $this->prepareDataAllByMask($order, $paginator);
		return $selection;
    }

    public function getDataAllByBinary($filter, $order, Paginator $paginator = NULL)
    {
		$selection = $this->prepareDataAllByBinary($order, $paginator);
		return $selection;
    }

    public function tableByUser()
    {
		$grid = $this->processingTable->createByUser();
		$grid->setDataSourceCallback([$this, 'getDataAllByAuthor']);
		$grid->setPagination(4, [$this, 'getDataSourceSumAuthor']);
		return $grid;
    }

    public function tableByMaterial($materialId)
    {
		$grid = $this->processingTable->createByMaterial();
		$this->materialId = $materialId;
		$grid->setDataSourceCallback([$this, 'getDataAllByMaterial']);
		$grid->setPagination(4, [$this, 'getDataSourceSumMaterial']);
		return $grid;
    }

    public function tableByMask($maskId)
    {
		$grid = $this->processingTable->createByMask();
		$this->maskId = $maskId;
		$grid->setDataSourceCallback([$this, 'getDataAllByMask']);
		$grid->setPagination(4, [$this, 'getDataSourceSumMask']);
		return $grid;
    }

    public function tableByBinary($binaryId)
    {
		$grid = $this->processingTable->createByBinary();
		$this->binaryId = $binaryId;
		$grid->setDataSourceCallback([$this, 'getDataAllByBinary']);
		$grid->setPagination(4, [$this, 'getDataSourceSumBinary']);
		return $grid;
    }

}
