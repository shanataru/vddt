<?php

namespace App\Data;

use App\Data\Entities;
use Nette;
use Kdyby;
use App\Data\BaseFacade as ADBF;
use Nette\Utils\Paginator;

class Processings extends ADBF {

    public function __construct(Kdyby\Doctrine\EntityManager $entityManager)
    {
		$this->entityManager = $entityManager;
		$this->repository = $entityManager->getRepository(Entities\ProcessingDAO::class);
    }

    /**
     * vrati entitu Processing nebo NULL
     * @return null|Entities\Processing
     */
    public function makeNew($name, $dateStart, $author, $mask = NULL, $binary = NULL, $path = NULL, $size = NULL, $preview = NULL, $material = NULL, $status = 2, $rating = NULL, $public = 1)
    {
        $new = new Entities\ProcessingDAO($name, $dateStart, $author, $mask, $binary, $path, $size, $preview, $material, $status, $rating, $public);
        $this->entityManager->persist($new);
        $this->entityManager->flush();
        return $new;
    }

    /**
     * @param $name
     * vrati entitu Processing nebo NULL
     * @return null|Entities\Processing
     */
    public function findByName($name)
    {
	   return $this->repository->findBy(array('name' => $name));
    }

    /**
     * @param $id
     * vrati entitu Processing nebo NULL
     * @return mixed|null|object
     */
    public function findById($id)
    {
	   return $this->repository->findOneBy(array('id' => $id));
    }

    public function findByStartDate($dateStart)
    {
	   return $this->repository->findOneBy(array('dateStart' => $dateStart));
    }

    public function findAll()
    {
	   return $this->repository->findAll();
    }

    public function countAll()
    {
	   return count($this->repository->findAll());
    }

    public function findAllByAuthor($userId, $order = NULL, Paginator $paginator = NULL)
    {
        return $this->findAllByKey(array('author' => $userId), $order, $paginator, 'dateStart');
    }

    public function findAllByMaterial($materialId, $order = NULL, Paginator $paginator = NULL)
    {
        return $this->findAllByKey(array('material' => $materialId), $order, $paginator, 'dateStart');
    }

    public function findAllByMask($maskId, $order = NULL, Paginator $paginator = NULL)
    {
        return $this->findAllByKey(array('mask' => $maskId), $order, $paginator, 'dateStart');
    }

    public function findAllByBinary($binaryId, $order = NULL, Paginator $paginator = NULL)
    {
        return $this->findAllByKey(array('binary' => $binaryId), $order, $paginator, 'dateStart');
    }

    public function countByMaterialId($materialId)
    {
	   $selection = $this->repository->findBy(array('material' => $materialId));
	   return count($selection);
    }

    public function countByMaskId($maskId)
    {
	   $selection = $this->repository->findBy(array('mask' => $maskId));
	   return count($selection);
    }

    public function countByBinaryId($binaryId)
    {
	   $selection = $this->repository->findBy(array('binary' => $binaryId));
	   return count($selection);
    }

    public function findAllByStatus($status)
    {
	   return $this->repository->findBy(array('status' => $status));
    }

    public function countByAuthorId($userId)
    {
        $selection = $this->repository->findBy(array('author' => $userId));
        return count($selection);
    }

}
