<?php

namespace App\Presenters;

use Nette;
use App\Model;

class ConversionPresenter extends BasePresenter {

    /** @var Model\User @inject */
    public $user;

    /** @var Model\Material @inject */
    public $material;

    public function renderTest($userId) {
	//$this->userPageId = $userId;
	//$this->template->userData = $this->user->getStats($this->userPageId);
		echo ">> conversion started <br>";
		$old_path = getcwd();
		echo ">> current directory " . $old_path . "<br />";
		$exec_path = $old_path . "/data/conversion";
		echo ">> new directory " . $exec_path . "<br />";
		chdir($exec_path);
		
		$dir = ".";
		if (is_dir($dir)) {
    		if ($dh = opendir($dir)) {
        	while (($file = readdir($dh)) !== false) {
				echo "++ filename " . $file . "<br />";
        	}
        	closedir($dh);
			}
		}
		$command = "./ffmpeg -i big_grass.avi -ss 00:00:00.000 -pix_fmt rgb24 -r 10 -s 320x240 -t 00:00:05.000  output.gif";
		$output = shell_exec($command);
		echo ">> output " . $output . "<br />"; 
		chdir($old_path);
    }

}
