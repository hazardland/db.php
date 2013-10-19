<?php

    //this is simple showcase to demonstrate basic framework abilities.
    //framework components and additional functionality are described in next samples step by step

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
         * length 100
         * @var string
         */
        public $name;
        /**
         * @var group
         */
        public $group;
        /**
         * enum
         * @var option
         */
        public $option;
        public function __construct ($name, $group=null, $option=null)
        {
            $this->name = $name;
            $this->group = $group;
            $this->option = $option;
        }
    }

    /**
     * cache load
     * engine myisam
     * table user_group
     */
    class group extends \db\entity
    {
        /**
         * primary
         * @var int
         */
        public $id;
        /**
         * length 100
         * @var string
         */
        public $name;
        public function __construct ($name)
        {
            $this->name = $name;
        }
    }

    class option extends \db\entity
    {
        public $id;
        /**
         * locale
         */
        public $name;
        public function __construct ($name_en, $name_fr)
        {
            $this->name_en = $name_en;
            $this->name_fr = $name_fr;
        }
    }


    $database = new \db\database ('db_samples', new \db\link ('default', $config->database, $config->username, $config->password));
    $database->link('default')->debug = true;

    $database->locales (array(new \db\locale('en'),new \db\locale('fr')));

    $database->add ('user');
    $database->add ('group');
    $database->add ('option');

    $database->update ();


    $groups = $database->group->load (\db\by('name','User'));
    if ($groups)
    {
        $group = reset ($groups);
    }
    else
    {
        $group = new group ('User');
        $database->save ($group);
    }

    \db\debug ($group);

    $query = new \db\query();
    $query->order ('name','asc');
    $options = $database->option->load ($query);
    if (!$options)
    {
        $options = array ();
        $options[] = new option ('Option 1 in English', 'Option 2 in French');
        $options[] = new option ('Option 2 in English', 'Option 2 in French');
        $options[] = new option ('Option 3 in English', 'Option 2 in French');
        $options[] = new option ('Option 4 in English', 'Option 2 in French');
        $database->save ($options);
    }

    \db\debug ($options);

    $user = new user ('User '.rand(1,100));
    $user->group = $group;
    $user->option[] = reset ($options);
    $user->option[] = end ($options);

    $database->save ($user);

    \db\debug ($user);

    $database->option->delete ($options);


?>
