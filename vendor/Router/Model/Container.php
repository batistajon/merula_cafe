<?php

namespace Router\Model;

class Container {

	public static function getModel($model) {
		$class = "\\Src\\Models\\".ucfirst($model);
		$conn = Connection::getDb();

		return new $class($conn);
	}
}
