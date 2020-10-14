<?php

namespace Router\Model;

require '../merula_cafe_ps/src/Config.php';

class Connection {

	public static function getDb() {
		try {

			$conn = new \PDO(
				ROUTER_DB_CONFIG["driver"].":host=".ROUTER_DB_CONFIG["host"].";dbname=".ROUTER_DB_CONFIG["dbname"],
				ROUTER_DB_CONFIG["username"],
				ROUTER_DB_CONFIG["passwd"]
			);

			return $conn;

		} catch (\PDOException $e) {
			$e->getMessage();
		}
	}
}