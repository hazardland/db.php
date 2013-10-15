<?php

    //in this sample we will learn how to modify field settings
    //it is done by documenting class and class properties
    //it is improtant that documentation section starts with line /** and ends with line */

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

    $database = new \db\database ();
    $database->link (new \db\link ('default', $config->database, $config->username, $config->password));
    $database->link('default')->debug = true;
    $database->default = 'db_samples';

    $database->add ('user');

    //so if you are coming here from previous example
    //where user.name had not any configs you must have field 'name' in 'user' table char(128)
    //** and after runing update field 'name' will become char(100) because you specified it in length comment in public $name documentation
    $database->update ();

    \db\debug ($database->user);

?>
