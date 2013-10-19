<?php

    include '../db.php';

    //here we define simple class
    class user
    {
        public $id;
        public $name;
    }

    //now lets see what happens when table eats the class:
    //pass class name to table constructor (in case of namespace
    //pass '\namespace1\namespace2\class_name'
    //or '.namespace1.namespace2.class_name')

    $table = new \db\table ('user');

    \db\debug ($table);

?>
