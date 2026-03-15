<?php 
namespace App\Libraries;

class Column{

	public $key;
	public $alias;
	public $type = 'column';
	public $callback;
	public $searchable = TRUE;
	public $orderable = TRUE;

}

