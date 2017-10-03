<?php
namespace App\Model;
use Nette\Security\User;
use Nette\Utils\Image;
use Nette;

class Model {

    /** @var Nette\Security\User */
    protected $user;

    const PATH = __DIR__ . "/../../www/data/"; 
    const PATHSHORT = "/data/"; 
    const PREVIEWPATH = __DIR__ . "/../../www/data/preview/"; 
    const PREVIEWPATHSHORT = "/data/preview/"; 
    const MATERIALPATH = __DIR__ . "/../../www/data/material/"; 
    const MATERIALPATHSHORT = "/data/material/"; 
    const MASKPATH = __DIR__ . "/../../www/data/mask/"; 
    const MASKPATHSHORT = "/data/mask/"; 
    const BINARYPATH = __DIR__ . "/../../www/data/binary/"; 
    const BINARYPATHSHORT = "/data/binary/"; 

    public function __construct(User $user)
    {
        $this->user = $user;
    }


    /* --------------------------------------------------------------------
    * Všeobecně užitečné věci
    * -------------------------------------------------------------------- */

    /* Zformátuje počet bajtů do stringu */
    public function filesizeFormatted($size)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }
    
    /** Spočítá kolik itemů je viditelných z pohledu přihlášeného uživatele. 
     *  @param itemsArray pole nějakých DAO, které mají atribut autor
     *  @param userId id uživatele z jehož pohledu se to vyhodnocuje
     */
    protected function countVisible($itemsArray, $userId)
    {
        $visibleCount = 0;
        foreach ($itemsArray as $item)
        {
			if($item->author == $userId || $item->public == 1){
                $visibleCount += 1;
            }
		}
		return $visibleCount;     
    }

    /* Vrátí true, pokud id přihlášeného uživatele se shoduje s tím vloženým */
    protected function checkSession($userId){
        if ($this->user->id == $userId)
        {
            return true;
        }
        return false;
    }


    /* --------------------------------------------------------------------
    * Zpracování souborů při uploadu materiálu
    * -------------------------------------------------------------------- */
    
    
    /* Vytvoří preview */
    protected function createPreview($file, $directory, $fileName)
    {
        $imagePreview = Image::fromFile(self::PATH . $directory . "/" . $fileName);
        $imagePreview->resize(800, 600);
        $imagePreview->save(self::PREVIEWPATH . $fileName, 80, Image::JPEG);
    }
    
    /* Přesune soubory do příslušných složek */
    protected function moveFile($file, $directory, $fileName)
    {
        $path = self::PATH . $directory . "/" . $fileName;
        $file->move($path);
    }
    
    /* Vygeneruje random jméno a zkontroluje jestli již soubor neexistuje */
    protected function createName($fileExt)
    {
        do {
            $filelName = uniqid(rand(0, 20), TRUE) . $fileExt;
            $pathPreview = self::PREVIEWPATH . $filelName;
        } while (file_exists($pathPreview));
        
        return $filelName;
    }
     
    /* Oddělení přípony */
    protected function getFormat($file)
    {
        return strtolower(mb_substr($file->getSanitizedName(), strrpos($file->getSanitizedName(), ".")));
    }

}
