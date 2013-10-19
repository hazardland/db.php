<?php

    //in this sample we will create new user and than save
    //also will make database init code minimal

    include './000.config.php';

    include '../db.php';

    class user
    {
        /**
         * primary
         * length 11
         * @var int
         */
        public $id;
        /**
         * data char
         * length 100
         * @var string
         */
        public $name;
    }

    $database = new \db\database ('db_samples', new \db\link ('default', $config->database, $config->username, $config->password));
    $database->link('default')->debug = true;

    $database->add ('user');

    $database->update ();

    //create lazarus
    $lazarus = new \user;
    $lazarus->name = 'Lazarus';

    //save lazarus
    $database->user->save ($lazarus);

    //kill lazarus
    unset ($lazarus);

    //resurrect lazarus
    $lazarus = $database->user->load (1);

    \db\debug ($lazarus);

    //1 stands for lazarus id
    //as soon as it is the first row in user table
    //it will have id 1 auto generated

?>
