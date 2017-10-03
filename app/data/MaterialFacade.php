<?php

namespace App\Data;

use App\Data\Entities;
use Nette;
use Kdyby;
use App\Data\BaseFacade as ADBF;
use Nette\Utils\Paginator;

class Materials extends ADBF {

    private $previewFacade;
    private $maskTextureFacade;

    public function __construct(Kdyby\Doctrine\EntityManager $entityManager, Previews $previewFacade, MaskTextures $maskTexture)
    {
		$this->entityManager = $entityManager;
        $this->previewFacade = $previewFacade;
        $this->maskTextureFacade = $maskTexture;
		$this->repository = $entityManager->getRepository(Entities\TextureDAO::class);
    }

    public function makeNew($name, $height, $width, $format, $size, $author, $preview, $path, $take = NULL, $move = NULL, $takenAtDate = NULL, $takenAtLocation = NULL, $public = 1, $note = NULL)
    {
        //pozor processing je nastaveno na NULL!!!
        $new = new Entities\TextureDAO($name, $height, $width, $format, $size, $author, $preview, $path, $take, $move, $takenAtDate, $takenAtLocation, $public, NULL, $note);
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
        $item = $this->repository->findOneBy(array('id' => $id));
        return $this->convertTime($item, 'dateUpload'); //pokud je $item null, funkce nic neudělá
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

    public function findAll($order = NULL) {
	    return $this->repository->findBy(array(), $order[0] ? array($order[0] => $order[1]) : NULL);
    }

    public function countAll()
    {
	   return count($this->repository->findAll());
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

    public function findAllByMask($maskId, $order = NULL, Paginator $paginator = NULL)
    {
        $maskTextures = $this->maskTextureFacade->findAllByMask($maskId);
        $materialsIds = [];
        foreach($maskTextures as $item)
        {
            $materialsIds[] = $item->texture;
        }

        $materials = $this->findAllByKey(array('id' => $materialsIds), $order, $paginator, 'dateUpload');
        
        return $materials;
    }
	
	public function delete($materialId)
    {
		$material = $this->repository->findOneBy(array('id' => $materialId));
        $this->previewFacade->delete($material->preview);
		$this->entityManager->remove($material);
		$this->entityManager->flush();
	}
}


