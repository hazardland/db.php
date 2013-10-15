<?php

    include '../db.php';

//    $link = new \db\link ('mysql','mysql:host=127.0.0.1','root','1234');
//    foreach ($link->select ("select site_blank.`users_groups`.`group_id`,site_blank.`users_groups`.`group_name`,site_dating.`users_groups`.`group_name` from site_blank.users_groups left join site_dating.users_groups on site_blank.users_groups.group_id=site_dating.users_groups.group_id") as $row)
//    {
//        debug ($row);
//    }
//    debug ($link->error());
//
//    $result = $link->value ("select group_name from site_blank.users_groups");
//    debug ($result);
//
//    $class = new \ReflectionClass('\\db\\foo');
//    $property = $class->getProperty ('name');
//
//    $result = \db\flag::set ($property);
//    debug ($result);


//    $table = new \db\table ('user');
//    debug ($table);

    /**
     * charset utf8
     * engine myisam
     * unique name
     * unique search id,name
     * index fast id,name
     * engine myisam
     */
    class user extends \db\entity
    {
        /**
         * @var int
         * length 10
         * unsigned
         */
        public $id;
        /**
         * @var string
         * rename name
         * ignore
         */
        public $login;
        /**
         * @var group
         */
        public $group;
        /**
         * after id
         * @var string
         */
        public $name;
        /**
         * @var float
         */
        public $mobile;
    }

    /**
     */
    class group extends \db\entity
    {
        /**
         * length 11
         * primary
         * default 1
         * first
         * after id
         * @var int
         */
        public $id;
        public $name;
        public $caption;
    }

    $database = new \db\database ('blackhole_blank');
    $database->link (new \db\link ('mysql','mysql:host=127.0.0.1','root','1234'));
    $database->add ('user');
    $database->add ('group');
    $database->update (); //creates all the tables and fields automatically or affects changes


?>
