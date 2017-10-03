<?php

namespace App\Data;

use App\Data\Entities;
use Nette;
use Kdyby;
use App\Data\BaseFacade as ADBF;
use Nette\Utils\Paginator;

class Masks extends ADBF {

    private $previewFacade;
    private $maskTextureFacade;

    public function __construct(Kdyby\Doctrine\EntityManager $entityManager, Previews $previewFacade, MaskTextures $maskTexture)
    {
		$this->entityManager = $entityManager;
		$this->repository = $entityManager->getRepository(Entities\MaskDAO::class);
		$this->maskTextureFacade = $maskTexture;
        $this->previewFacade = $previewFacade;
    }

    public function makeNew($name, $height, $width, $format, $author, $preview, $path, $universal = 0, $public = 1, $note = NULL, $size = NULL)
    {
        $new = new Entities\MaskDAO($name, $height, $width, $format, $author, $preview, $path, $universal, $public, $note, $size);
        $this->entityManager->persist($new);
        $this->entityManager->flush();
        return $new;
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

    public function findAll()
    {
	   return $this->repository->findAll();
    }

    public function countAll()
    {
	   return count($this->repository->findAll());
    }

    public function countByMaterialId($materialId)
    {
	   return $this->maskTextureFacade->countByMaterialId($materialId);
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
    
    public function findAllByMaterial($materialId, $order = NULL, Paginator $paginator = NULL)
    {

        $maskTextures = $this->maskTextureFacade->findAllByMaterial($materialId);
        $maskIds = [];
        foreach($maskTextures as $item)
        {
            $maskIds[] = $item->mask;
        }

        $masks = $this->findAllByKey(array("id" => $maskIds), $order, $paginator, 'dateUpload');
        return $masks;
    }

    /* Přidá masku k textuře, pokud již je přiřazena, nic nedělá */
    public function addMaskToTexture($maskId, $textureId)
    {
        $maskTexture = $this->maskTextureFacade->findByMaskTexture($maskId, $textureId);
        if (!$maskTexture)
        {
            $this->maskTextureFacade->makeNew($maskId, $textureId);
        }
    }
    
    /* Odebere masku textuře, pokud ho textura nemá, pak nic nedělá */
    public function removeMaskFromTexture($maskId, $textureId)
    {
        $maskTextures = $this->maskTextureFacade->findByMaskTexture($maskId, $textureId);
        if ($maskTextures)
        {
            foreach($maskTextures as $maskTexture)
            {
                $this->maskTextureFacade->delete($maskTexture->id);   
            }
        }
    }

    public function delete($maskId)
    {
        $mask = $this->repository->findOneBy(array('id' => $maskId));
        $this->previewFacade->delete($mask->preview);
        $this->entityManager->remove($mask);
        $this->entityManager->flush();
    }

}
