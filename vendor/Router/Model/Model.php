<?php

namespace Router\Model;

abstract class Model
{
	protected $db;

	public function __construct(\PDO $db) {
		$this->db = $db;
	}
}