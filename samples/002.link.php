<?php

    include './000.config.php';

    include '../db.php';

    //create database link
    //first parameter of link stands for link name
    //in this case 'default' is link name
    $link = new \db\link ('default', $config->database, $config->username, $config->password);
    $link->debug = true;

    //simple query fetch via link
    foreach ($link->query ("show tables from mysql") as $row)
    {
        \db\debug ($row);
    }

    //error fetch from link
    $result = $link->query ("select nothing from void");
    if ($link->error())
    {
        \db\debug ($link->error());
    }

    //single value fetch from link
    $value = $link->value ("select 1");
    \db\debug ($value);

    //single row fetch from link
    $row = $link->fetch ("select 1,2,3,4");
    \db\debug ($row)
?>
