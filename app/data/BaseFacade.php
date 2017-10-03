<?php

namespace App\Data;
use Kdyby;
use Nette;
use Nette\Utils\Paginator;

class BaseFacade extends Nette\Object {

    /**
     * @var Kdyby\Doctrine\EntityManager
     */
    protected $entityManager;
	protected $repository;

    public function __construct(Kdyby\Doctrine\EntityManager $entityManager) {
		$this->entityManager = $entityManager;
    }
	
    public function itemEdited(){
        $this->entityManager->flush();
    }
    
     /**
     * @var $item jedno DAO
     * @var $varName jméno proměnné obsahující čas
     */
    protected function convertTime($item, $varName)
    {
        if ($item && property_exists($item, $varName))
        {
            $item->{$varName} = \Nette\Utils\DateTime::createFromFormat('d.m.Y H:i', $item->{$varName}->format('d.m.Y H:i'));
        }
        return $item;
    }
    
    /**
     * @var $array string jméno klíče => hodnota, ...
     * @var $order řazení
     * @var $paginator stránkovač
     * @var $timeName jméno atributu v objektu výsledného DAO, který bude přepsaán do "použitelného" formátu
     */
    protected function findAllByKey($keyArray, $order = NULL, Paginator $paginator = NULL, $timeName = NULL)
    {
        $selection = $this->repository->findBy($keyArray,
                                               $order[0] ? array($order[0] => $order[1]) : NULL,
                                               $paginator ? $paginator->getItemsPerPage() : NULL,
                                               $paginator ? $paginator->getOffset() : NULL);
        foreach ($selection as $item)
        {
			$item = $this->convertTime($item, $timeName);
		}
		return $selection;
    }
}