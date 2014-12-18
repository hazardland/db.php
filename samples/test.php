<?php

    include '../db.php';

    $database = new \db\database ('mysql:host=127.0.0.1', 'test', 'root', '1234');

    $database->debug ();
    $result = $database->link()->query ('show databases');

    echo "<pre>";
    if ($result)
    {
    	foreach ($result as $row)
    	{
    		var_export($row);
    	}
    }
    echo "</pre>"

?>