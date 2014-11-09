<?php

require_once("db/dbparam.php");
require_once("db/db.php");

function __autoload($class) {
	require_once("classes/$class.class.php");
}

?>
