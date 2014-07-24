<?php

    include './000.config.php';

    include '../db.php';

    class user
    {
        public $id;
        public $name;
    }

    //create new database instance
    $database = new \db\database ();

    //add default link to database (links can be as many as you wish)
    $database->link (new \db\link ('default', $config->database, $config->username, $config->password));

    //debut link to see what queries database runs
    $database->link('default')->debug = true;

    //specify default database name (default database name is used in case if class does not have specified database name)
    $database->default = 'db_samples';

    //add declared class to database
    //* important 1 - as class has no database specified - engine will asume that it is located in default database, in this case 'db_samples'
    //* important 2 - as class has no link specified - engine will asume that it is located on default link
    $database->add ('user');

    //and run magic command
    //after wich engine will analize all added classes (in this case user)
    //will detect if class' database exists on table specified link and if not will create it (in this case db_sample)
    //so in this case 'db_samples' database will be created on default link
    //and user table will be created on default link in 'db_samples' database
    $database->update ();

    //added class specific handler is located in $database->tables
    //and also has reference to $database->{class}
    //in this case we cand find user class handler below:
    \db\debug ($database->user);


?>
