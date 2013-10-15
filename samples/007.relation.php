<?php

    //in this sample we will add new class 'group'
    //new object of group
    //new field user.group
    //and relate user.group to group object

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

        /**
         * @var group
         */
        public $group;
        public function __construct ($id, $login, $group=null)
        {
            $this->id = $id;
            $this->login = $login;
            $this->group = $group;
        }
    }

    //take a look how user.group relates to group by adding in public $group comment directive '@var group'

    //important! - if you want class to be master extend it from \db\entity because \db\entity always has public $id
    //in this case group extends \db\entity
    //this gives db engine a clue that group will always have id property
    //therefore it can be used for relation
    class group extends \db\entity
    {
        public $id;
        public $name;
        public function __construct ($id, $name)
        {
            $this->id = $id;
            $this->name = $name;
        }
    }

    $database = new \db\database ('db_samples', new \db\link ('default', $config->database, $config->username, $config->password));
    $database->link('default')->debug = true;

    $database->add ('user');
    $database->add ('group');

    $database->update ();

    $administrator = $database->group->load (1);
    if (!$administrator)
    {
        $administrator = new group (null,'Administrator');
        $database->group->save ($administrator);
    }

    \db\debug ($administrator);

    $john = new user (1, 'John');
    //here the relation happend !
    $john->group = $administrator;

    //here if save is not happening
    //it means we already have user with id 1
    if (!$database->user->save ($john, \db\query::insert))
    {
        //than update it to save group
        $database->user->save ($john);
    }

    //unset used variables for better way to see miracle
    unset ($john, $administrator);

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
