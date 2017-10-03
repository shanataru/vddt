<?php

namespace App\Data;

use App\Data\Entities;
use Nette;
use Kdyby;

class Products extends Nette\Object {

    public function __construct(Kdyby\Doctrine\EntityManager $entityManager)
    {
		$this->entityManager = $entityManager;
		$this->repository = $entityManager->getRepository(Entities\TextureDAO::class);
    }

    public function makeNew($name, $height, $width, $format, $size, $author, $preview, $path, $processing, $take = NULL, $move = NULL, $takenAtDate = NULL, $takenAtLocation = NULL, $public = 1, $note = NULL)
    {
        //pozor processing nemuze byt NULL!!! - product nejak vznikl
        $new = new Entities\TextureDAO($name, $height, $width, $format, $size, $author, $preview, $path, $take = NULL, $move = NULL, $takenAtDate = NULL, $takenAtLocation = NULL, $public = 1, $processing, $note = NULL);
        $this->entityManager->persist($new);
        $this->entityManager->flush();
        return $new;
    }

    /**
     * @param $id
     * @return mixed|null|object
     */
    public function findById($id)
    {
	   return $this->repository->findOneBy(array('id' => $id));
    }

    /**
     * @param $preview
     * @return mixed|null|object
     */
    public function findByPreview($preview)
    {
	   return $this->repository->findOneBy(array('preview' => $preview));
    }

    /**
     * @param $path
     * @return mixed|null|object
     */
    public function findByPath($path)
    {
	   return $this->repository->findOneBy(array('path' => $path));
    }

    /**
     * @param $path
     * @return mixed|null|object
     */
    public function findByProcessing($processing)
    {
	   return $this->repository->findOneBy(array('processing' => $processing));
    }

    public function countByAuthorId($userId)
    {
	   $selection = $this->repository->findBy(array('author' => $userId));
	   return count($selection);
    }

}
