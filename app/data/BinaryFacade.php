<?php

namespace App\Data;

use App\Data\Entities;
use Nette;
use Kdyby;
use App\Data\BaseFacade as ADBF;
use Nette\Utils\Paginator;

class Binaries extends ADBF
{

    public function __construct(Kdyby\Doctrine\EntityManager $entityManager)
    {
		$this->entityManager = $entityManager;
		$this->repository = $entityManager->getRepository(Entities\BinaryDAO::class);
    }

    public function makeNew($name, $author, $path, $public = 1, $fileSize = NULL, $note = NULL, $language = NULL)
    {
		$new = new Entities\BinaryDAO($name, $author, $path, $public, $fileSize, $note, $language);
		$this->entityManager->persist($new);
		$this->entityManager->flush();
		return $new;
    }

    public function countAll()
	{
		return count($this->repository->findAll());
    }

    /**
     * @param $author
     * @return null|Entities\MaskDAO
     */
    public function findByAuthor($author)
    {
		return $this->repository->findOneBy(array('author' => $author));
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
     * @param $path
     * @return mixed|null|object
     */
    public function findByPath($path)
    {
		return $this->repository->findOneBy(array('path' => $path));
    }

    public function findAll()
    {
		return $this->repository->findAll();
    }

    public function countByAuthorId($userId)
    {
		$selection = $this->repository->findBy(array('author' => $userId));
		return count($selection);
    }

    public function findAllByAuthor($userId, $order = NULL, Paginator $paginator = NULL)
    {
        return $this->findAllByKey(array('author' => $userId), $order, $paginator, 'dateUpload');
    }

}
