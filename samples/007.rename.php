<?php

    //in this sample we will rename name field to login
    //and we will add one more field to user

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
         * rename name
         * data char
         * length 100
         * @var string
         */
        public $login;
        public function __construct ($id, $login)
        {
            $this->id = $id;
            $this->login = $login;
        }
    }

    //if you are coming from previous example
    //'name' field will be renamed to 'login'
    //just by using directive 'rename [old_field_name]' in public $login documentation

    $database = new \db\database ('db_samples', new \db\link ('default', $config->database, $config->username, $config->password));
    $database->link('default')->debug = true;

    $database->add ('user');

    $database->update ();

    if (!$database->user->load(1))
    {
        //idea of second parameter is to force insert object even it has id
        //without second parameter it will just update object with id!==null
        $database->user->save (new user(1,'John'), \db\query::insert);
    }


    $user = $database->user->load (1);

    \db\debug ($user);

    /*
      output :
      user::__set_state(array(
          'id'  =  '1',
          'login'  =  'John',
    ))
    */

?>
