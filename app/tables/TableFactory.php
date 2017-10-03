<?php

namespace App\Tables;

use Nette;
use Nextras\Datagrid\Datagrid;


class TableFactory
{
	use Nette\SmartObject;

	public function create() {
		$grid = new Datagrid();
		return $grid;
	}
}
