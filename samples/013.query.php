<?php

	include '../db.php';
	include './013.model.php';

	$database = new \db\database ($config->hostname,
								  $config->database,
								  $config->username,
								  $config->password);

	$database->scan ('.galaxy');

	//truncate previous load table records
	foreach ($database->context->tables as &$table)
	{
		$database->link($table->link)->query("truncate ".$table->name());
	}

	$alpha = $database->save (new \galaxy\star('alpha', 4));
	$sun = $database->save (new \galaxy\star('sun', 1));

	$database->save (new \galaxy\planet($alpha,'xxx',2,false,100));
	$database->save (new \galaxy\planet($alpha,'xxx',2,false,123));

?>