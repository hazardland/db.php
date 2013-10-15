<?php


    include '../db.php';



    echo (\date('Z')/3600)."<br>";
    echo \db\time()."<br>";
    echo \db\time(null, \db\time())."<br>";
    echo \db\time(\db\time(null,\db\time()))."<br>";
    $user->zone = -4*3600;
    echo \db\time(null, \db\time())."<br>";
    echo \db\time('2013-10-15 15:38:36');

?>
